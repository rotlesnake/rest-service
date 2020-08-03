<?php

namespace App\Controllers\Api;

use App\Plugins\Core\Core;
use App\Plugins\Core\HelperFieldTypes;
use App\Plugins\Core\PhpParser;
use App\Controllers\ApiBaseController;

use App\Models\Roles;
use App\Models\System\Models;
use App\Models\Menu;

require_once APP_PATH.'Plugins/WIDEIMAGE/index.php';

class TableController extends ApiBaseController
{

    public function index($request, $response, $args)
    {
        return "ERR";
    }



    public function anyAction($request, $response, $action, $args)
    {
       if ($action=="get") { 
          return $this->getTableRows($request, $response, $args[0], $args); 
       } else { 
          return $this->postTableRow($request, $response, $action, $args[0], $args); //add, edit, delete
       }
    }

    //******************* GET *******************************************************
    public function getTableRows($request, $response, $tablename, $args)
    {
        $json_response = $this->add_error(0);
        $req_params = $request->getParams();
        $user = $this->auth->getUserFields();
        $json_response["info"] = [];
        $json_response["rows"] = [];
        $json_response["pagination"] = [];
        if ($tablename=="") return "tablename empty";

        $modelClass = Core::tablenameToModel($tablename);
        if (strlen($modelClass)==0) return "table \"$action\" not found";
        
        $tableInfo = $modelClass::modelInfo();
        $allowFields=["id"];

        //оставляем только поля разрешенные для чтения   или запрашиваемые клиентом
        //_fields:['id','name',....]
        foreach ($tableInfo["columns"] as $x=>$y) {
           if (isset($req_params["_fields"]) && !in_array($x, $req_params["_fields"]))  continue;
           if (isset($y["is_virtual"]) && $y["is_virtual"]) continue;
           if ($this->auth->hasRoles($y["roles_read"])) array_push($allowFields, $x);
        }

        //Сортировка по умолчанию из модели
        if (isset($tableInfo["sortBy"]) && (!isset($req_params["sortBy"]) || (isset($req_params["sortBy"]) && count($req_params["sortBy"])==0))  ) {
           $req_params["sortBy"]   = $tableInfo["sortBy"];
           if (isset($tableInfo["sortDesc"])) $req_params["sortDesc"] = $tableInfo["sortDesc"];
        }

        $MODEL=null;
        //если доступ запрещен то выдаем сообщение
        if (!$this->auth->hasRoles($tableInfo["roles_read"])) return json_encode($this->add_error(3));

        //Значения по умолянию
        if (!isset($tableInfo["itemsPerPage"])) $tableInfo["itemsPerPage"] = 100;
        if (!isset($tableInfo["itemsPerPageVariants"])) $tableInfo["itemsPerPageVariants"] = [10,50,100,200,300,500];
        if (!isset($req_params["itemsPerPage"])) $req_params["itemsPerPage"]=$tableInfo["itemsPerPage"];
        if ((int)$req_params["itemsPerPage"]<0)  $req_params["itemsPerPage"]=$tableInfo["itemsPerPage"];
        if (!isset($req_params["page"])) $req_params["page"]=1;

        //Это дочерняя таблица - тогда фильтруем записи по родителю
        //&_parent_table=users&_parent_id=1
        if (isset($req_params["_parent_table"]) && (int)$req_params["_parent_id"]>0) {
             $parent_field = "";
             foreach ($tableInfo["parent_tables"] as $x=>$y) {
                 if ($y["table"]==$req_params["_parent_table"]) $parent_field = $y["id"];
             }
             $MODEL = $modelClass::select($allowFields)->filterRead()->where($parent_field, (int)$req_params["_parent_id"] );
        }

        $isFiltered = false;
        //Запрашивают фильтр записей по полям
        if (isset($req_params["_search"])) {
            foreach ($req_params["_search"] as $x=>$y) { //перебираем поля в запросе
                if (isset($req_params["_search"][$x]["value"]) && strlen($req_params["_search"][$x]["value"])>0) {  //поле есть в поиске
                    $isFiltered = true;
                    $s_filed=$req_params["_search"][$x]["field"]; $s_oper=$req_params["_search"][$x]["oper"]; $s_value=$req_params["_search"][$x]["value"];
                    if ($tableInfo["columns"][$s_filed]["type"]=="Date")     { $s_value = Core::convDateToSQL($s_value, false); }
                    if ($tableInfo["columns"][$s_filed]["type"]=="Datetime") { $s_value = Core::convDateToSQL($s_value, true); }
                    if ($s_oper=="like")   { $s_value = "%".$s_value."%"; }
                    if ($s_oper=="begins") { $s_oper="like"; $s_value = $s_value."%"; }

                    if ($MODEL==null) {
                       $MODEL = $modelClass::select($allowFields)->filterRead()->where($s_filed, $s_oper, $s_value);
                    } else {
                       $MODEL = $MODEL->where($s_filed, $s_oper, $s_value);
                    }
                }
            }
            if ($isFiltered) {
                $all_records = $MODEL->count();
                $MODEL = $MODEL->offset(($req_params["page"]-1)*$req_params["itemsPerPage"])->limit($req_params["itemsPerPage"]);
                if (isset($req_params["sortBy"]) && isset($req_params["sortBy"][0]) && strlen(isset($req_params["sortBy"][0]))>0 ) $MODEL = $MODEL->orderBy($req_params["sortBy"][0], $req_params["sortDesc"][0]);
                if (isset($req_params["sortBy"]) && isset($req_params["sortBy"][1]) && strlen(isset($req_params["sortBy"][1]))>0 ) $MODEL = $MODEL->orderBy($req_params["sortBy"][1], $req_params["sortDesc"][1]);
                $rows = $MODEL->get();
            }
        }
        //фильтр по ИД
        if (isset($req_params["_search_id"])) {
            $isFiltered = true;
            $all_records = 1;
            $rows = $modelClass::select($allowFields)->filterRead()->where("id", $req_params["_search_id"])->get();
        } 
        
        //Фильтров нет - выдаем все записи 
        if (!$isFiltered) {
            //полный запрос данных
            if ($MODEL==null) { 
                $MODEL = $modelClass::select($allowFields)->filterRead();
            }
            $all_records = $MODEL->count();
            $MODEL = $MODEL->offset(((int)$req_params["page"]-1)*(int)$req_params["itemsPerPage"])->limit($req_params["itemsPerPage"]);
            if (isset($req_params["sortBy"]) && isset($req_params["sortBy"][0]) && strlen(isset($req_params["sortBy"][0]))>0 ) $MODEL = $MODEL->orderBy($req_params["sortBy"][0], $req_params["sortDesc"][0]);
            if (isset($req_params["sortBy"]) && isset($req_params["sortBy"][1]) && strlen(isset($req_params["sortBy"][1]))>0 ) $MODEL = $MODEL->orderBy($req_params["sortBy"][1], $req_params["sortDesc"][1]);
            $rows = $MODEL->get();
        }

//die($MODEL->toSql());


        //Выдаем информацию о таблице
        $json_response['info'] = $tableInfo;
        //Если нет поля ID то добавляем его
        if (!isset($json_response['info']['columns']["id"])) { array_push($json_response['info']['columns'], ["id"=>HelperFieldTypes::typeInteger("id")->width(50)->roles_read([2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20])->roles_edit([])->get()]  ); }

        //Редактируем колонки, убираем лишние поля
        foreach ($json_response['info']["columns"] as $x=>$y) {
            if (isset($req_params["_fields"]) && $x!="id" && !in_array($x, $req_params["_fields"])) { unset($json_response['info']["columns"][$x]); continue; } //Оставляем только те поля которые запросили
            if (!$this->auth->hasRoles($y["roles_read"])) { unset($json_response['info']["columns"][$x]); continue; } //Если чтение запрещено то удаляем
            if (!$this->auth->hasRoles($y["roles_edit"])) { $json_response['info']["columns"][$x]["protected"]=true; continue; } //Если редактирование запрещено то делаем отметку о защищенном поле
        }

        //Заполняем информацию о странице
        $json_response['pagination'] = [
                                "key"=> "id",
                                "page"=> $req_params["page"],
                                "totalItems"=> (($all_records<=$req_params["itemsPerPage"])?-1:$all_records),
                                "itemsPerPage"=> $req_params["itemsPerPage"],
                                "rowsPerPageItems"=> $tableInfo["itemsPerPageVariants"]
                                ];

        //Берем строки из таблицы и выдаем клиенту
        $need_footer = false;
        $footer_row = [];
        foreach ($rows as $row) {
            $item = $this->rowConvert($tableInfo, $row); //Форматируем поля для вывода клиенту
            array_push($json_response['rows'], $item);

            foreach ($json_response['info']["columns"] as $x=>$y) {
                if (isset($y["footer_row"])) { 
                   if (!isset($footer_row[$x])) $footer_row[$x] = 0;
                   $footer_row[$x] += (float)$item[$x]; 
                   $need_footer = true; 
                }
            }
        }

        //Итоги таблицы
        if ($need_footer) $json_response['footer_row'] = $footer_row;

        return json_encode($json_response);
    }

    public function rowConvert($tableInfo, $row){
            $item = [];
            $item["id"] = $row->id;
            //Каждую строку разбираем на поля, проверяем уровни доступа,заполняем
            foreach ($tableInfo["columns"] as $x=>$y) {
              if (!$this->auth->hasRoles($y["roles_read"])) continue;
              $item[$x] = $row->{$x};
              if ($y["type"]=="Linktable" || $y["type"]=="Select") { 
                 $item[$x."_text"] = $row->getLinkedRows($x,true); 
                 if (isset($y["as_object"]) && $y["as_object"]) $item[$x."_rows"] = $row->getLinkedRows($x); 
              } 
              if ($y["type"]=="Checkbox") { $item[$x."_text"] = ((int)$row->{$x}==1?"Да":"Нет"); } 
              if ($y["type"]=="Images")   { $item[$x] = $this->getUploadedFiles(json_decode($item[$x]), "image", $tableInfo["table"], $row->id, $x); }
              if ($y["type"]=="Files" )   { $item[$x] = $this->getUploadedFiles(json_decode($item[$x]), "file", $tableInfo["table"], $row->id, $x); }
              if ($y["type"]=="Password") $item[$x] = "";
              if ($y["type"]=="Date")     $item[$x] = Core::convDateToDate($item[$x], false);
              if ($y["type"]=="Datetime") $item[$x] = Core::convDateToDate($item[$x], true);
            }
        return $item;
    }

    public function getUploadedFiles($files_array, $type, $table_name="", $row_id=0, $field_name=""){
       $files = [];
       if (!is_array($files_array)) return $files;
       if (count($files_array)==0)  return $files;

       foreach ($files_array as $y) {
         $fname = $y;
         $fpath = FULL_URL."api/uploads/$type/?file=".$table_name."/".$row_id."_".$field_name."_".$y;
         array_push($files, ["name"=>$fname, "url"=>$fpath]);
       }
       return $files;
    }





    public function prepareFileUploads($files_array, $table_name="", $row_id=0, $field_name="", $field_params=[]){
              if (!is_array($files_array)) return false;

              $files=[];
              for($i=0; $i<count($files_array); $i++) {
                if (!isset($files_array[$i]["name"])) continue;
                if (!isset($files_array[$i]["src"]))  continue;

                $fname = Core::getSlug($files_array[$i]["name"],true);
                $fsrc = $files_array[$i]["src"];
                if (strlen($fname)<2) continue;
                if (strlen($fsrc)<8)  continue;
                $fsrc = substr($fsrc, strpos($fsrc, 'base64,')+7 );
                array_push($files, $fname );

                if ($row_id>0) {
                   $folder_path = ROOT_PATH."uploads/".$table_name;
                   if ( !is_dir($folder_path) ) { mkdir($folder_path, 0777); }
                   file_put_contents($folder_path."/".$row_id."_".$field_name."_".$fname, base64_decode($fsrc) );

                   if ($field_params['type']=='Images' && isset($field_params['resize'])) {  //resize and crop image
                      $file_name = $folder_path."/".$row_id."_".$field_name."_".$fname;
                      $image = initWideImage($file_name);
                      if ((int)$field_params['resize'][0]==0) $field_params['resize'][0]=null;
                      if ((int)$field_params['resize'][1]==0) $field_params['resize'][1]=null;
                      $image = $image->resize($field_params['resize'][0], $field_params['resize'][1], $field_params['resize'][2], 'down');
                      if (isset($field_params['crop']) && (int)$field_params['crop'][0]>0 && (int)$field_params['crop'][1]>0) {
                        $image = $image->crop($field_params['crop'][2], $field_params['crop'][3], $field_params['crop'][0], $field_params['crop'][1]);
                      }
                      $image->saveToFile($file_name);
                   }
                }
              }
              if (count($files)>0) return json_encode($files);

        return false;
    }

    public function fillRowParams($row, $action, $tableInfo, $params)
    {
        foreach ($tableInfo["columns"] as $x=>$y) {
          if (isset($y["is_virtual"]) && $y["is_virtual"]) continue;      //Поле виртуальное
          if (!isset($params[$x]))  continue;                             //Поле отсутствует
          if (!$this->auth->hasRoles($y["roles_edit"])) continue;         //Нет прав не заполняем поле
          if ($y["type"]=="Password" && strlen($params[$x])<4) continue;  //Пароль пустой не заполняем

          //Если картики или фалы то подготавливаем массив названий 
          if ($y["type"]=="Images" || $y["type"]=="Files") {
              $files = $this->prepareFileUploads($params[$x], $tableInfo["table"], $row->id, $x, $y);
              if ($files) { $row->{$x} = $files; }
          } else {
              $row->{$x} = $params[$x];
              if (is_array($params[$x])) {  $row->{$x} = Core::arrayToString($params[$x]);  } //прочий массив преобразуем в строку [12,32,34] ->  12,32,34
          }

          //пароль хешируем
          if ($y["type"]=="Password") $row->{$x} = password_hash($params[$x], PASSWORD_DEFAULT);
          //при добавлении поля если оно пустое то заполняем его значение по умолчанию
          if (!empty($y["default"]) && $action=="add" && strlen($params[$x])==0) $row->{$x} = $y["default"];
          //Меняем даты в формат SQL
          if ($y["type"]=="Date") $row->{$x} = Core::convDateToSQL($row->{$x}, false);
          if ($y["type"]=="Datetime") $row->{$x} = Core::convDateToSQL($row->{$x}, true);
        }
        return $row;
    }



    //********************* POST *********************************************************************************************************************
    public function postTableRow($request, $response, $action, $tablename, $args)
    {
        $json_response = $this->add_error(0);
        $req_params = $request->getParams();
        $user_id = $this->auth->getUserFields()['id'];

        $id = 0; 
        if (isset($req_params["id"])) { $id = (int)$req_params["id"]; }

        $modelClass = Core::tablenameToModel($tablename);
        if (strlen($modelClass)==0) return "table not found";

        $tableInfo = $modelClass::modelInfo();
        if (!$this->auth->hasRoles($tableInfo["roles_".$action]))  return json_encode($this->add_error(3)); //access denied
        
        //Находим строку в таблице
        if ($action=="add")    { $row = new $modelClass(); try { $row->created_by_user = $user_id; } catch(Exception $e) {}  }
        if ($action=="edit")   { $row = $modelClass::filterRead()->filterEdit()->where("id",$id)->first(); }
        if ($action=="delete") { $row = $modelClass::filterRead()->filterEdit()->filterDelete()->where("id",$id)->first();   $row->delete(); return json_encode( $this->add_error(0, ["action"=>$action, "row"=>$row, "result"=>true ]) ); }
        if (!$row) {return json_encode( $this->add_error(4) ); } //если не нашли строку то выходим

        $row = $this->fillRowParams($row, $action, $tableInfo, $req_params);  //Заполняем строку данными формы

        //Это дочерняя таблица - тогда устанавливаем родителя
        //&_parent_table=users&_parent_id=1
        if (isset($req_params["_parent_table"]) && (int)$req_params["_parent_id"]>0) {
             $parent_field = "";
             foreach ($tableInfo["parent_tables"] as $x=>$y) {
                 if ($y["table"]==$req_params["_parent_table"]) $parent_field = $y["id"];
             }

             $row->{$parent_field} = (int)$req_params["_parent_id"];
        }

        $result = $row->save();                 //Сохраняем
        if (method_exists($modelClass, "afterPostRow")) { 
           $modelClass::afterPostRow($row, $req_params);        //Отправляем событие 
        }

        if (!$result) {return json_encode( $this->add_error(4) ); }  //Если ошибка сохранения то сообщаем и выходим

        if ($action=="add")   { $row = $this->fillRowParams($row, $action, $tableInfo, $req_params); } //Если добавление новой строки то еще раз обновляем данные файлов

        $id = $row->id;
        $row = $modelClass::filterRead()->filterEdit()->where("id",$id)->first(); //Считываем данные из базы и отдаем клиенту

        $item = $this->rowConvert($tableInfo, $row);

        return json_encode($this->add_error(0, [
                            "action"=>$action, 
                            "result"=>true,
                            "row"=>$item,
                           ]));
    }

}
