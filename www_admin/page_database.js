Vue.component('page_database', {
template:`
<div>
    <v-data-table dense
        multi-sort
        item-key="model" single-select v-model="selected"
        @click:row="onRowClick"        
        :headers="columns" :items="rows"
        :loading="isLoading" loading-text="Загрузка..." class="elevation-4"
        :items-per-page="1000" :server-items-length="-1"
        group-by="module" 
        :footer-props="{'items-per-page-options': [100,500,1000], 'show-current-page':true}" hide-default-footer
        @dblclick:row="dblClickRow"
    >

        <template v-slot:group.header="{group,toggle,isOpen}">
            <td colspan="9">
                <v-btn icon @click.stop="toggle()"><v-icon>{{(isOpen ? 'keyboard_arrow_down':'keyboard_arrow_right')}}</v-icon></v-btn>
                {{group}}
                <div class="d-inline ml-4 caption grey--text">{{modulesInfo[group]}}</div>
            </td>
        </template>
        <template v-slot:group.summary>
            <td colspan="9" style="height:2px;">
            </td>
        </template>

        <template v-slot:footer>
            <v-divider />
            <div class="py-3">

                <v-tooltip top color="green">
                    <template v-slot:activator="{ on }">
                        <v-btn class="mx-2" color="primary" fab small v-on="on" @click="showDialogAddModel()">
                            <v-icon dark>add</v-icon>
                        </v-btn>
                    </template>
                    <span>Добавить модель</span>
                </v-tooltip>


                <v-tooltip top color="primary">
                    <template v-slot:activator="{ on }">
                        <v-btn class="mx-2" color="primary" fab small v-on="on" @click="showDialogEditModel()" :disabled="selected.length==0">
                            <v-icon dark>edit</v-icon>
                        </v-btn>
                    </template>
                    <span>Изменить модель</span>
                </v-tooltip>

            </div>
        </template>

    </v-data-table>


    <!-- dialog !-->
    <component :is="dialog" v-model="modules"  :module="editModule" :model="editModel" :table="editTable" @close="dialog=null; reloadTable();"></component>


</div>
`,
    data() {
        return {
            isLoading: false,
            columns:[],
            rows:[],
            selected:[],

            dialog:null,
            modules:[],
            modulesInfo:{},
            editModule:'',
            editModel:'',
            editTable:''
        };
    },
    mounted(){
        this.reloadTable();
    },  
    
    methods: {
        loader(status){
            this.$store.commit("SHOW_LOADER", status);
        },

        reloadTable(){
            this.isLoading = true;

            axios({method:"POST", url:"api/database_get.php", data:{} }).then(response=>{
                this.isLoading = false;
                this.columns = this.convertColumns(response.data.columns);
                this.rows = response.data.rows;

                this.modules = [];
                this.modulesInfo = {};
                response.data.modules.forEach(e=>{
                    this.modules.push(e.name);
                    this.modulesInfo[e.name] = e.desc;
                });
            }).catch(error=>{
                this.isLoading = false;
            });
        },//reloadTable

        convertColumns(columns){
            var rez = [];
            for (let item in columns) {
                var obj = columns[item];

                rez.push(obj);
            }
            return rez;
        },

        onRowClick(row, isMultiple) {
            const index = this.rows.indexOf(row);
            row._selected_index = index;
            this.selected = [];
            this.selected.push(row);
            
            this.editModule = this.selected[0].module;
            this.editModel = this.selected[0].model;
            this.editTable = this.selected[0].table;
        },
        dblClickRow(evt, row){
            //console.log(row.item.module, row.item.model)
            this.editModule = row.item.module;
            this.editModel = row.item.model;
            this.editTable = row.item.table;
            this.showDialogEditModel();
        },

        showDialogAddModel(){
            this.dialog = dialog_add_table;
        },
        showDialogEditModel(){
            this.dialog = dialog_edit_model;
        },
    },//methods

});
