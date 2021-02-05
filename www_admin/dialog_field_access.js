Vue.component('dialog_field_access', {
template:`
<section class="ma-4">

    <v-dialog v-model="active" persistent scrollable fullscreen>
        <v-card>
            <v-toolbar dense color="primary" elevation="0">
                <v-toolbar-title class="white--text">Настройки доступа к полю ({{field.name}})</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn icon dark @click="close()">
                    <v-icon>close</v-icon>
                </v-btn>
            </v-toolbar>

            <v-card-text class="pt-5" v-if="active">

                <v-form ref="form" v-model="form_valid">
                        <v-select v-model="field.read"   :items="rolesList" label="Доступ на чтение" persistent-hint :hint="field.read.toString()" chips deletable-chips item-value="id" item-text="name" multiple outlined class="ml-4"></v-select>
                        <v-select v-model="field.add"    :items="rolesList" label="Доступ на добавление" persistent-hint :hint="field.add.toString()" chips deletable-chips item-value="id" item-text="name" multiple outlined class="ml-4"></v-select>
                        <v-select v-model="field.edit"   :items="rolesList" label="Доступ на изменение" persistent-hint :hint="field.edit.toString()" chips deletable-chips item-value="id" item-text="name" multiple outlined class="ml-4"></v-select>

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

</section>
`,

    data() {
        return {
            active: false,
            form_valid:false,

            index: 0,
            field: {},
            rolesList:[],
        };
	},
	
        
    methods: {
        show(item, index,rolesList){
            this.field = JSON.parse( JSON.stringify(item) );
            this.index = index;
            this.rolesList = rolesList;

            this.active = true;
        },

        save(){
            this.$refs.form.validate();
            if (!this.form_valid) return;

			this.active = false;
			this.$emit("save", this.index, this.field.read, this.field.add, this.field.edit);
        },
		
		close(){
			this.active = false;
			this.$emit("close");
		},
    },


});
