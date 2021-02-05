Vue.component('dialog_edit_model', {
template:`
<div>

    <v-dialog v-model="active" fullscreen scrollable>
        <v-card>
            <v-toolbar dense color="primary" elevation="0">
                <v-toolbar-title class="white--text">Редактирование модели <span v-if="modelInfo">({{modelInfo.table}}) {{modelInfo.name}}</span></v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn icon dark @click="close()">
                    <v-icon>close</v-icon>
                </v-btn>
            </v-toolbar>

            <v-card-text class="pt-5" v-if="modelInfo">
                <v-form ref="form" v-model="form_valid">
                    <div class="ma-2 title">Наименование таблицы</div>
                    <v-row class="mx-0">
                        <v-text-field v-model="modelInfo.name" label="Наименование таблицы" placeholder="Наименование таблицы" outlined></v-text-field>
                        <v-text-field v-model="modelInfo.category" label="Категория" placeholder="Категория таблицы" outlined class="ml-4"></v-text-field>
                    </v-row>

                    <div class="ma-2 title">Сортировка / Кол-во записей</div>
                    <v-row class="mx-0">
                        <v-select v-model="modelInfo.sortBy[0]" :items="columns" item-value="name" item-text="label" label="Сортировка по полю" outlined ></v-select>
                        <v-select v-model="modelInfo.itemsPerPage" :items="modelInfo.itemsPerPageVariants" label="Кол-во записей на одной странице" outlined class="ml-4"></v-select>
                    </v-row>


                    <div class="ma-2 title">Права доступа</div>
                        <v-select v-model="modelInfo.read"   :items="rolesList" label="Доступ на чтение данных" persistent-hint :hint="modelInfo.read.toString()" chips deletable-chips item-value="id" item-text="name" multiple outlined class="ml-4"></v-select>
                        <v-select v-model="modelInfo.add"    :items="rolesList" label="Добавление записи" persistent-hint :hint="modelInfo.add.toString()" chips deletable-chips item-value="id" item-text="name" multiple outlined class="ml-4"></v-select>
                        <v-select v-model="modelInfo.edit"   :items="rolesList" label="Изменение записи" persistent-hint :hint="modelInfo.edit.toString()" chips deletable-chips item-value="id" item-text="name" multiple outlined class="ml-4"></v-select>
                        <v-select v-model="modelInfo.delete" :items="rolesList" label="Удаление записи" persistent-hint :hint="modelInfo.delete.toString()" chips deletable-chips item-value="id" item-text="name" multiple outlined class="ml-4"></v-select>


                    <div class="ma-2 title">Форма редактирования</div>
                    <v-row class="mx-0">
                        <v-text-field v-model="modelInfo.type" label="Наименование формы редактирования" placeholder="Наименование формы редактирования" outlined></v-text-field>
                    </v-row>

                    <div class="ma-2 title">Фильтрация записей по полям  &nbsp; <v-icon @click="()=>{filters.push({});}" large color="primary">add</v-icon> </div>
                    <div v-for="(item,i) in filters" :key="i">
                        <v-row class="mx-0">
                            <v-select v-model="item.name"  :items="columns" item-value="name" item-text="label" label="Поле фильтрации" outlined clearable></v-select>
                            <v-select v-model="item.filterType"  :items="['like','=','>','<','in']"  label="Тип фильтрации" outlined></v-select>
                            <v-text-field v-model="item.label" label="Наименование фильтра" placeholder="Наименование фильтра" outlined clearable></v-text-field>
                        </v-row>
                    </div>

                    <v-divider />
                    <div class="ma-2 display-1">ПОЛЯ ТАБЛИЦЫ</div>

                    <div v-for="(item,i) in columns" :key="i+1000" class="my-6">
                        <v-row class="mx-0">
                            <v-text-field v-model="item.name" label="Наименование поля (en)" placeholder="Наименование поля (en)"  outlined dense hide-details class="ml-4" style="max-width:300px;" :disabled="item.name=='id' || item.name=='created_at' || item.name=='updated_at' || item.name=='created_by_user'"></v-text-field>
                            <v-text-field v-model="item.label" label="Наименование поля (ru)" placeholder="Наименование поля (ru)" outlined dense hide-details class="ml-4" style="max-width:300px;"></v-text-field>
                            <v-select     v-model="item.type"  :items="columnTypes"  label="Тип данных поля" outlined dense hide-details class="ml-4" :disabled="item.name=='id' || item.name=='created_at' || item.name=='updated_at' || item.name=='created_by_user'"></v-select>
                            <v-btn color="blue" dark style="margin:2px 0 0 12px" @click="showFieldSettings(item,i)"> <v-icon size="36">settings</v-icon></v-btn>
                            <v-btn color="green" dark style="margin:2px 0 0 12px" @click="showFieldAccess(item,i)"> <v-icon size="32">how_to_reg</v-icon></v-btn>
                        </v-row>
                    </div>


                    <v-divider class="mt-4" />
                    <v-btn color="primary" class="ma-4" @click="()=>{columns.push({read:[], add:[], edit:[]});}"> <v-icon size="32" left>add</v-icon> Добавить поле </v-btn>
                    <v-divider />


                    <pre>
                    
                    </pre>
                </v-form>
            </v-card-text>

            <v-divider></v-divider>
            <v-card-actions>
                <v-btn class="mx-2" color="error" @click="close()">
                    <v-icon left>close</v-icon> Закрыть
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn class="mx-2" color="primary" @click="save()">
                    <v-icon left>save</v-icon> Сохранить
                </v-btn>
            </v-card-actions>

        </v-card>        
    </v-dialog>

    <dialog_field_access ref="field_access" @save="fieldAccessChange" />
    <dialog_field_settings ref="field_settings" @save="fieldSettingsChange" />
</div>
`,
    components:{
    },
    props:{
        module:String,
        model:String
    },
    data() {
        return {
            active: true,
            form_valid:false,

            modelInfo: null,
            columns:[],
            filters:[],
            columnTypes:[],
            rolesList:[],
        };
	},
	
    mounted(){
            this.loader(true);
            axios({method:"POST", url:"api/database_model_info.php", data:{module:this.module, model:this.model} }).then(response=>{
                this.loader(false);
                this.modelInfo = response.data.model;
                this.rolesList = response.data.roles;
                this.columnTypes = response.data.column_types;

                this.columns = [];
                for (let item in this.modelInfo.columns) {
                    var obj = this.modelInfo.columns[item];
                    obj.name = item;
                    this.columns.push(obj);
                }
                this.filters = [];
                for (let item in this.modelInfo.filter) {
                    var obj = this.modelInfo.filter[item];
                    obj.name = item;
                    this.filters.push(obj);
                }

            }).catch(error=>{
                this.loader(false);
            });            
        
    },  
    
    methods: {
        loader(status){
            if (status) { Swal.showLoading(); } else { Swal.close(); }
        },

        //--- Field access -------------------------------------------------------------------
        showFieldAccess(item, i){
            this.$refs.field_access.show(item, i, this.rolesList);
        },
        fieldAccessChange(index, read_acc, add_acc, edit_acc){
            this.columns[index].read = read_acc;
            this.columns[index].add = add_acc;
            this.columns[index].edit = edit_acc;
            this.$forceUpdate();
        },
        //----------------------------------------------------------------------
        //--- Field settings -------------------------------------------------------------------
        showFieldSettings(item, i){
            this.$refs.field_settings.show(item, i);
        },
        fieldSettingsChange(index, data){
            for (let i in data) {
                this.columns[index][i] = JSON.parse(JSON.stringify(data[i]));
            }
            this.$forceUpdate();
        },
        //----------------------------------------------------------------------

        save(){
            this.$refs.form.validate();
            if (!this.form_valid) return;

            this.loader(true);
            axios({method:"POST", url:"api/database_model_edit.php", data:{module:this.module, model:this.model, info:this.modelInfo} }).then(response=>{
                this.loader(false);
                if (response.data.error>0) {
                    return;
                }
                this.close();
            }).catch(error=>{
                this.loader(false);
            });            
        },
		
		close(){
			this.active = false;
			this.$emit("close");
		},
    },//methods

});
