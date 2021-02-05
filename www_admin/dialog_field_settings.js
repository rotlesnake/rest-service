Vue.component('dialog_field_settings', {
template:`
<section class="ma-4">

    <v-dialog v-model="active" persistent scrollable fullscreen>
        <v-card>
            <v-toolbar dense color="primary" elevation="0">
                <v-toolbar-title class="white--text">Настройки поля ({{field.name}})</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn icon dark @click="close()">
                    <v-icon>close</v-icon>
                </v-btn>
            </v-toolbar>

            <v-card-text class="pt-5" v-if="active">

                <v-form ref="form" v-model="form_valid">

                    <div class="ma-2 title">Общие настройки</div>
                    <v-text-field v-model="field.width" label="Ширина в таблице" placeholder="200" outlined clearable hide-details dense></v-text-field>
                    <v-checkbox v-model="field.hidden" :label="'Невидимо в таблице (прятать): '+(field.hidden?'Да':'Нет')" hide-details></v-checkbox>
                    <v-checkbox v-model="field.disabled" :label="'Не редактируемое: '+(field.disabled?'Да':'Нет')" hide-details></v-checkbox>

                    <v-text-field class="my-4" v-model="field.placeholder" label="Подсказка внутри элемента (placeholder)" placeholder="" outlined clearable hide-details dense></v-text-field>
                    <v-text-field class="my-4" v-model="field.hint" label="Подсказка внизу элемента (hint)" placeholder="" outlined clearable hide-details dense></v-text-field>
                    <v-row class="mx-0 my-4">
                        <v-text-field class="" v-model="field['prepend-icon']" label="Иконка в начале" placeholder="" outlined clearable hide-details dense></v-text-field>
                        <v-text-field class="ml-4" v-model="field['append-icon']" label="Иконка в конце" placeholder="" outlined clearable hide-details dense></v-text-field>
                    </v-row>
                    <v-row class="mx-0 my-4">
                        <v-text-field class="" v-model="field['color']" label="Цвет элемента" placeholder="" outlined clearable hide-details dense></v-text-field>
                        <v-text-field class="ml-4" v-model="field['background-color']" label="Фоновый цвет элемента" placeholder="" outlined clearable hide-details dense></v-text-field>
                    </v-row>
                    <v-row class="mx-0 my-4">
                        <v-text-field class="" v-model="field['prefix']" label="Текст в начале поля" placeholder="" outlined clearable hide-details dense></v-text-field>
                        <v-text-field class="ml-4" v-model="field['suffix']" label="Текст в конце поля" placeholder="" outlined clearable hide-details dense></v-text-field>
                    </v-row>
                    <v-checkbox v-model="field.dense" :label="'Сжатое поле: '+(field.dense?'Да':'Нет')" hide-details></v-checkbox>
                    <v-checkbox v-model="field['hide-details']" :label="'Не показывать нижнюю подсказку: '+(field['hide-details']?'Да':'Нет')" hide-details></v-checkbox>
                    <v-checkbox v-model="field.outlined" :label="'Внешняя обводка поля: '+(field.outlined?'Да':'Нет')" hide-details></v-checkbox>
                    <v-checkbox v-model="field.counter" :label="'Показывать счетчик символов: '+(field.counter?'Да':'Нет')" hide-details></v-checkbox>
                    <v-checkbox v-model="field.clearable" :label="'Показывать кнопку обнулить поле: '+(field.clearable?'Да':'Нет')" hide-details></v-checkbox>


                    <div class="mt-8 title">Проверка заполнения</div>
                    <v-text-field class="ml-0 mt-2" v-model="field.rules" label="Правила проверки" placeholder="[v=> v.length>0 || 'заполните поле']" outlined clearable></v-text-field>


                    <div class="mt-4" v-if="field.type=='string'">
                        <div class="ma-2 title">Маска ввода</div>
                        <v-row>
                            <v-checkbox v-model="field.masked" :label="'Использовать маску: '+(field.masked?'Да':'Нет')"></v-checkbox>
                            <v-text-field class="ml-4" v-if="field.masked" v-model="field.mask" label="Маска поля" placeholder="8(999)999-99-99" outlined clearable
                            hint="9-число *-любой символ" persistent-hint></v-text-field>
                        </v-row>
                    </div>

                    <div class="mt-4" v-if="field.type=='text'">
                        <div class="ma-2 title">Ввод текста</div>
                            <v-text-field class="ml-4" v-model="field.rows" label="Количество линий" placeholder="4" outlined clearable></v-text-field>
                    </div>

                    <div class="mt-4" v-if="field.type=='select'">
                        <div class="ma-1 title">Выбор из списка</div>

                    </div>

                    <div class="mt-4" v-if="field.type=='linkTable'">
                        <div class="ma-1 title">Выбор из таблицы</div>
                        <v-select v-model="field.typeSelect" :items="['table','combobox']" label="Способ выбора" outlined></v-select>

                        <v-select v-model="field.table"  :items="allTables"   item-value="name" item-text="label" label="Таблица" outlined clearable></v-select>
                        <v-select v-model="field.field"  :items="tableFields" item-value="name" item-text="label" label="Поле" outlined clearable hide-details></v-select>

                        <v-checkbox v-model="field.multiple" :label="'Мультивыбор: '+(field.multiple?'Да':'Нет')" hide-details></v-checkbox>
                    </div>


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

        };
	},
	
        
    methods: {
        show(item, index){
            this.field = JSON.parse( JSON.stringify(item) );
            this.index = index;

            this.active = true;
        },

        save(){
            this.$refs.form.validate();
            if (!this.form_valid) return;

			this.active = false;
			this.$emit("save", this.index, this.field);
        },
		
		close(){
			this.active = false;
			this.$emit("close");
		},
    },

});
