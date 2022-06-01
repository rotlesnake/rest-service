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
                    <v-text-field class="mt-2" v-model="field.formWidth" label="Ширина в форме" placeholder="100%" outlined clearable hide-details dense></v-text-field>
                    <v-text-field class="mt-2" v-model="field.divider" label="Разделительный текст между полями" placeholder="Заголовок раздела" outlined clearable hide-details dense></v-text-field>
                    <v-text-field class="mt-2" v-model="field.caption" label="Заголовок поля" placeholder="Заголовок поля" outlined clearable hide-details dense></v-text-field>
                    <v-checkbox v-model="field.hidden" :label="'Невидимо в таблице (прятать): '+(field.hidden?'Да':'Нет')" hide-details></v-checkbox>
                    <v-checkbox v-model="field.disabled" :label="'Не редактируемое: '+(field.disabled?'Да':'Нет')" hide-details></v-checkbox>
                    <v-checkbox v-model="field.visible" :label="'Принудительное отображение поля в форме, даже если поле не редактируемое: '+(field.visible?'Да':'Нет')" hide-details></v-checkbox>

                    <v-text-field class="my-4" v-model="field.placeholder" label="Подсказка внутри элемента (placeholder)" placeholder="" outlined clearable hide-details dense></v-text-field>
                    <v-text-field class="my-4" v-model="field.hint" label="Подсказка внизу элемента (hint)" placeholder="" outlined clearable hide-details dense></v-text-field>
                    <v-row class="mx-0 my-4">
                      <v-col cols="6">
                        <v-text-field class="" v-model="field['prepend-icon']" label="Иконка в начале" placeholder="" outlined clearable hide-details dense></v-text-field>
                        <v-text-field class="" v-model="field['prepend-icon-text']" label="Подсказка при клике" placeholder="" outlined clearable hide-details dense></v-text-field>
                      </v-col>
                      <v-col cols="6">
                        <v-text-field class="ml-4" v-model="field['append-icon']" label="Иконка в конце" placeholder="mdi-information-outline" outlined clearable hide-details dense></v-text-field>
                        <v-text-field class="ml-4" v-model="field['append-icon-text']" label="Подсказка при клике" placeholder="текст с html тегами" outlined clearable hide-details dense></v-text-field>
                      </v-col>
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


                    <div class="mt-6 title">Проверка заполнения</div>
                    <v-text-field class="ml-0 mt-2" v-model="field.rules" label="Правила проверки заполнения" placeholder="[v=> v.length>0 || 'заполните поле']" hide-details outlined clearable dense></v-text-field>
                    <v-text-field class="ml-0 mt-2" v-model="field.vif" label="Условия отображения" placeholder="[status] == 1 || IN([status], 1)" hide-details outlined clearable dense></v-text-field>
                    <v-text-field class="ml-0 mt-2" v-model="field.afterChange" label="Действие после изменения значения этого поля" placeholder="[status]<=2 ? SET('type',1) ;; IN([status],3) ? SET('type',2)" hide-details outlined clearable dense></v-text-field>
                    <v-text-field class="ml-0 mt-2" v-model="field.default" label="Значение по умолчнию для Базы" placeholder="0" hide-details outlined clearable dense></v-text-field>
                    <v-text-field class="ml-0 mt-2" v-model="field.defaultFront" label="Значение по умолчнию для Интерфейса" placeholder="now или 'Да' или 123" hide-details outlined clearable dense></v-text-field>


                    <div class="mt-4" v-if="field.type=='string'">
                        <div class="ma-2 title">Маска ввода</div>
                        <v-row>
                            <v-checkbox v-model="field.masked" :label="'Использовать маску: '+(field.masked?'Да':'Нет')"></v-checkbox>
                            <v-text-field class="ml-4" v-if="field.masked" v-model="field.mask" label="Маска поля" placeholder="8(999)999-99-99" outlined clearable
                            hint="9-число *-любой символ (спец маски: int float email amount)" persistent-hint dense></v-text-field>
                            <v-checkbox v-if="field.masked" v-model="field.unmask" :label="'Убирать маску после ввода: '+(field.masked?'Да':'Нет')"></v-checkbox>
                        </v-row>
                    </div>

                    <div class="mt-4" v-if="field.type=='time'">
                        <div class="ma-2 title">Ввод времени</div>
                        <v-row>
                            <v-select class="mx-2" v-model="field.step"  :items="[{value:'1', text:'00:00 - Часы:Минуты'},{value:'2', text:'00:00:00 - Часы:Минуты:Секунды'}]"  label="Тип ввода" outlined clearable dense></v-select>
                        </v-row>
                    </div>

                    <div class="mt-4" v-if="field.type=='text'">
                        <div class="my-2 title">Ввод текста</div>
                            <v-text-field class="ml-4" v-model="field.rows" label="Количество линий" placeholder="4" outlined clearable dense></v-text-field>
                    </div>

                    <div class="mt-4" v-if="field.type=='select' || field.type=='selectText'">
                        <div class="mt-2 title">Выбор из списка</div>
                        <v-checkbox v-model="field.multiple" :label="'Мультивыбор: '+(field.multiple?'Да':'Нет')" class="mt-n2 mb-4" hide-details></v-checkbox>
                        <v-checkbox v-model="field.chips" :label="'Обернуть в чипсу: '+(field.multiple?'Да':'Нет')" class="mt-n4 mb-4" hide-details></v-checkbox>
                        <v-row v-for="(item,i) in allItems" :key="i">
                            <v-col cols="2" class="py-1"> <v-text-field v-model="allItems[i].key" label="Значение" outlined clearable hide-details dense></v-text-field> </v-col>
                            <v-col cols="10" class="py-1"> <v-text-field v-model="allItems[i].value" label="Описание"  outlined clearable hide-details dense></v-text-field> </v-col>
                        </v-row>
                        <v-btn class="my-4" @click="addItem" color="green">+ Добавить пункт</v-btn>
                    </div>

                    <div class="mt-4" v-if="field.type=='linkTable'">
                        <div class="my-2 title">Выбор из таблицы</div>
                        <v-select v-model="field.typeSelect" :items="['table', 'tree', 'combobox']" label="Способ выбора" outlined></v-select>

                        <v-select v-if="false" v-model="field.table"  :items="allTables"   item-value="name" item-text="label" label="Таблица" outlined clearable dense></v-select>
                        <v-text-field v-model="field.table" label="Таблица" placeholder="Название таблицы (en)" outlined clearable hide-details dense></v-text-field>
                        <v-text-field class="mt-2" v-model="field.field" label="Поле" placeholder="Название поля (en)" outlined clearable hide-details dense></v-text-field>
                        <v-text-field class="mt-2" v-model="field.tableFilter" label="Фильтр записей при выборе" placeholder="('id', '=', 123) ;; ('type', '>', [from_type]) ;; ('status', 'in', [statuses])" outlined clearable hide-details dense></v-text-field>

                        <v-checkbox v-model="field.object" :label="'Отдавать все данные выбранной таблицы: '+(field.object?'Да':'Нет')" hide-details></v-checkbox>
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
            allTables:[],
            allItems:[],

        };
	},
	
        
    methods: {
        show(item, index){
            this.field = JSON.parse( JSON.stringify(item) );
            this.index = index;
            this.active = true;

            if (this.field.type=='select' || this.field.type=='selectText') {
               this.allItems = [];
               for (let i in this.field.items){
                   this.allItems.push({key:i, value:this.field.items[i]});
               }
            }
        },

        save(){
            this.$refs.form.validate();
            if (!this.form_valid) return;

            if (this.field.type=='select' || this.field.type=='selectText') {
                this.field.items = {};
                this.allItems.forEach(e=>{
                    this.field.items[e.key] = e.value;
                });
            }

            this.active = false;
            this.$emit("save", this.index, this.field);
        },
		
        close(){
            this.active = false;
            this.$emit("close");
        },

        addItem(){
            let i = this.allItems.length + 1;
            this.allItems.push({key:i, value:"описание"});
        },
    },

});
