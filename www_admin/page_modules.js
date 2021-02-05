Vue.component('page_modules', {
template:`
<div>
  <v-card>
    <v-card-title>
        Модули
		<v-spacer />
		<v-btn @click.stop="refresh()" icon> 
            <v-icon>mdi-refresh</v-icon>
        </v-btn>
	</v-card-title>
	<v-divider />
	<v-card-text>

        <v-expansion-panels>
            <v-expansion-panel v-for="(imodule,im) in modules" :key="im">
            <v-expansion-panel-header class="title">{{imodule.module}}</v-expansion-panel-header>
            <v-expansion-panel-content>
                <v-card v-for="(icontroller,ic) in imodule.controllers" :key="ic" class="ma-2 elevation-5">
                    <v-card-title class="py-1">
                        {{icontroller.controller}}<span style="color:#aaa">Controller.php</span>
                        <v-spacer />
                        <v-btn @click.stop="addMethodDialog(imodule.module, icontroller.controller)" color="primary" x-small> 
                            <v-icon left>mdi-plus</v-icon> Добавить метод
                        </v-btn>                        
                    </v-card-title>
                    <div v-for="(imethod,imt) in icontroller.methods" :key="imt">
                        <v-divider />
                        <v-card-title class="subtitle-1 py-1">
                            /{{icontroller.path}}{{imethod.name}}
                            <v-spacer />
                            <div class="caption" v-html="imethod.comment"></div>
                        </v-card-title>
                    </div>
                </v-card>
                <v-card-actions class="mt-4">
                    <v-spacer />
                    <v-btn @click.stop="addController(imodule.module)" color="primary" small> 
                        <v-icon left>mdi-plus</v-icon> Добавить контроллер
                    </v-btn>
                </v-card-actions>
            </v-expansion-panel-content>
            </v-expansion-panel>
        </v-expansion-panels>

	</v-card-text>
	<v-divider />
	<v-card-actions>
		<v-btn @click.stop="addModule()" color="primary"> 
            <v-icon left>mdi-plus</v-icon> Добавить модуль
        </v-btn>
        <v-spacer />
	</v-card-actions>
  </v-card>

    <!-- dialog !-->
    <v-dialog v-model="dialog" persistent max-width="1200px" scrollable>
        <v-card>
            <v-toolbar dense color="primary" elevation="0">
                <v-toolbar-title class="white--text">Добавление метода</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn icon dark @click="dialog=false">
                    <v-icon>close</v-icon>
                </v-btn>
            </v-toolbar>

            <v-card-text class="pt-4">
                <v-form ref="form" v-model="form_valid">
                    <div class="title my-2">{{module}}/{{controller}}</div>
                    <v-text-field v-model="methodName" label="Наименование метода (en)" outlined :rules="[v=> v && v.length>0 || 'Заполните поле']"></v-text-field>
                    <v-select     v-model="methodType" :items="methodTypes" label="Шаблон метода" outlined :rules="[v=> v && v.length>0 || 'Заполните поле']"></v-select>
                </v-form>
            </v-card-text>

            <v-divider></v-divider>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn class="mx-2" color="primary" @click="addMethod()">
                    <v-icon left>add_box</v-icon> Добавить
                </v-btn>
                <v-spacer></v-spacer>
            </v-card-actions>
        </v-card>        
    </v-dialog>

</div>
`,
    props:{
		name:{type:String, default:''},
    },

    data(){return {
            module:'',
            controller:'',
            dialog:null,
            form_valid:false,

            modules:[],

            dialog:false,
            methodTypes: [],
            methodType:'',
            methodName:'',
    }},

    mounted(){
        this.refresh();
    },  
    
    methods: {
	refresh(){
            this.modules = [];
            Swal.showLoading();

            axios({method:"POST", url:"api/modules_get.php", data:{a:1} }).then(response=>{
                this.modules = response.data.modules;
                Swal.close();
	    }).catch(e=>{
                Swal.close();
	    });
    },
        
        addModule(){
            Swal.fire({title: 'Введите название модуля', input: 'text', inputPlaceholder: 'Модуль (en)'}).then(response=>{
                if (response.isConfirmed && response.value.length>2) {
	            axios({method:"POST", url:"api/module_create.php", data:{name:response.value} }).then(response=>{
                        this.refresh();
                    }).catch(e=>{
                    });
                }
            });
        },

        addController(moduleName){
            Swal.fire({title: 'Введите название контроллера', input: 'text', inputPlaceholder: 'Для модуля '+moduleName}).then(response=>{
                if (response.isConfirmed && response.value.length>2) {
	            axios({method:"POST", url:"api/module_cotroller.php", data:{module:moduleName, name:response.value} }).then(response=>{
                        this.refresh();
                    }).catch(e=>{
                    });
                }
            });
        },


        addMethodDialog(moduleName, controllerName){
            this.module = moduleName;
            this.controller = controllerName;
            this.dialog = true;

            axios({method:"POST", url:"api/module_templates.php", data:{} }).then(response=>{
                this.methodTypes = response.data.methods;
            }).catch(error=>{
            });   
        },


        addMethod(){
            this.$refs.form.validate();
            if (!this.form_valid) return;

            axios({method:"POST", url:"api/module_method.php", data:{module:this.module, controller:this.controller, name:this.methodName, type:this.methodType} }).then(response=>{
                if (response.error>0) {
                    return;
                }
                this.dialog = false;
                this.refresh();
            }).catch(error=>{
            });            
        },

    },//methods

});
