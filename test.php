<?php
error_reporting(E_ALL);
require_once(__DIR__."/init.php");
error_reporting(E_ALL);

if (!isset($_SERVER["PHP_AUTH_USER"])) {
	Header("WWW-Authenticate: Basic realm=auth");
	Header("HTTP/1.0 401 Unauthorized");
	die();
} else {
    $login = $_SERVER["PHP_AUTH_USER"];
    $password = $_SERVER["PHP_AUTH_PW"];
}

echo '<meta charset="utf-8">';
echo '<body>';
echo "<h3>Проверка приложения</h3>";
echo "<b>".(\App\Settings::$description)."</b><br>";
echo "Физический путь к приложению: <b>".ROOT_PATH."</b><br>";
echo "Папка приложения: <b>".ROOT_URL."</b><br>";
echo "URL приложения: <b>".FULL_URL."</b><br>";
echo "Настройки: <pre>". print_r($settings,1)."</pre>";
echo "<hr>";


echo "<h3>Проверка папки vendor</h3>";
if (file_exists(ROOT_PATH."vendor")) {
    echo "<font color=green>Папка есть</font><br>";
} else {
    echo "<font color=red>Папки нет</font><br>";
}
echo "<hr>";


echo "<h3>Проверка .htaccess</h3>";
if (file_exists(ROOT_PATH.".htaccess")) {
    echo "<font color=green>Файл есть</font><br>";
} else {
    echo "<font color=red>Файла нет</font><br>";
}
echo "<hr>";


echo "<h3>Проверка подключения к базе</h3>";
$user = $APP->DB::table("users")->first();
if ($user) {
    echo "<font color=green>Успех</font><br>";
} else {
    echo "<font color=red>Ошибка</font><br>";
}
echo "Количество пользователей: <b>".($APP->DB::table("users")->count())."</b>";
echo "<hr>";


echo "<h3>Проверка rest api</h3>";
echo '</body>';

echo "
<script>
    async function addLog(txt) {
        let div = document.createElement('div');
        div.innerHTML  = txt;
        document.body.append(div);
    }
    async function logResponse(txt, response) {
        console.log(response);
        addLog('<b>'+txt+'</b>');
        addLog('<b>'+response.url+'</b>');
        addLog('<font color=blue>status: '+response.status+' ('+response.statusText+')</font>');
        addLog('body: '+(await response.text()));
        addLog('<hr>');
    }

    setTimeout(()=>{
        fetch('".ROOT_URL."auth/login/index',  {method: 'POST', body: JSON.stringify({login:'".$login."', password:'".$password."'})}).then(response=>{
            logResponse('".ROOT_URL."auth/login',response);
        }).catch(response=>{
            logResponse('/auth/login',response);
        });
    }, 0);

    setTimeout(()=>{
        fetch('".ROOT_URL."auth/logout').then(response=>{
            logResponse('".ROOT_URL."auth/logout',response);
        }).catch(response=>{
            logResponse('".ROOT_URL."auth/logout',response);
        });
    }, 1000);

</script>
";