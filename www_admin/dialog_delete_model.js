Vue.component('dialog_delete_model', {
template:`
<div>

    <v-dialog v-model="active">
        <v-card>
            <v-toolbar dense color="primary" elevation="0">
                <v-toolbar-title class="white--text">Удаление модели <span v-if="modelInfo">({{modelInfo.table}}) {{modelInfo.name}}</span></v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn icon dark @click="close()">
                    <v-icon>close</v-icon>
                </v-btn>
            </v-toolbar>

            <v-card-text class="pt-2" style="min-height:100px">
                  Точно удалить?
            </v-card-text>

            <v-divider></v-divider>
            <v-card-actions>
                <v-btn class="mx-2" color="primary" @click="close()">
                    <v-icon left>close</v-icon> Закрыть
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn class="mx-2" color="red" @click="del()">
                    <v-icon left>save</v-icon> Удалить
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
        model:String,
        table:String,
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
            tableParentChilds: 0,
            childrenTables: [{}],
            parentTables: [{}],
        };
	},
	
    mounted(){
            this.loader(true);
            axios({method:"POST", url:"api/database_model_info.php", data:{module:this.module, model:this.model, table:this.table} }).then(response=>{
                this.loader(false);
                this.modelInfo = response.data.model;
                this.rolesList = response.data.roles;
                this.columnTypes = response.data.column_types;

                this.tableParentChilds = 0,
                this.childrenTables = [{}];
                this.parentTables = [{}];
                if (this.modelInfo.childrenTables) { this.tableParentChilds = 1; this.childrenTables = this.modelInfo.childrenTables; }
                if (this.modelInfo.parentTables)   { this.tableParentChilds = 2; this.parentTables = this.modelInfo.parentTables;   }

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

        del(){
            this.loader(true);
            axios({method:"POST", url:"api/database_model_delete.php", data:{module:this.module, model:this.model, table:this.table} }).then(response=>{
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
