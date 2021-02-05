Vue.component('dialog_add_table', {
template:`
<div>

    <v-dialog v-model="active" persistent max-width="1200px" scrollable>
        <v-card>
            <v-toolbar dense color="primary" elevation="0">
                <v-toolbar-title class="white--text">Добавление модели (таблицы)</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn icon dark @click="close()">
                    <v-icon>close</v-icon>
                </v-btn>
            </v-toolbar>

            <v-card-text class="pt-4">
                <v-form ref="form" v-model="form_valid">
                    <v-select     v-model="module" :items="modules" label="Модуль" outlined :rules="[v=> v && v.length>0 || 'Заполните поле']"></v-select>
                    <v-text-field v-model="model" label="Наименование модели и таблицы (en)" outlined :rules="[v=> v && v.length>0 || 'Заполните поле']"></v-text-field>
                    <v-text-field v-model="label" label="Описание (ru)" outlined :rules="[v=> v && v.length>0 || 'Заполните поле']"></v-text-field>
                </v-form>
            </v-card-text>

            <v-divider></v-divider>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn class="mx-2" color="primary" @click="save()">
                    <v-icon left>add_box</v-icon> Добавить
                </v-btn>
                <v-spacer></v-spacer>
            </v-card-actions>
        </v-card>        
    </v-dialog>


</div>
`,
    model: {
        prop: 'modules',
        event: 'change'
    },
    props:{
        modules:Array
    },
    data() {
        return {
            active: true,
            form_valid:false,
            
            module: '',
            model:'',
            label:'',
        };
	},
	
    mounted(){
        
    },  
    
    methods: {

        save(){
            this.$refs.form.validate();
            if (!this.form_valid) return;

            Swal.showLoading();
            axios({method:"POST", url:"api/database_add_model.php", data:{module:this.module, model:this.model, label:this.label} }).then(response=>{
                Swal.close();
                if (response.error>0) {
                    this.$swal.toast("Ошибка: "+response.message, "error", "center-center", 3000);
                    return;
                }
                this.close();
            }).catch(error=>{
                Swal.close();
            });            
        },
		
		close(){
			this.active = false;
			this.$emit("close");
		},
    },//methods

});
