Vue.component('page_export', {
template:`
<div>

  <v-card>
    <v-card-title>
		<v-spacer />
		<v-btn color="primary" @click.stop="doExportSQL()"> Выполнить экспорт базы в SQL формат</v-btn>
		<v-spacer />
	</v-card-title>
    <v-card-title>
		<v-spacer />
		<v-btn color="primary" @click.stop="doExportCSV()"> Выполнить экспорт базы в CSV формат</v-btn>
		<v-spacer />
	</v-card-title>
	<v-divider />
	<v-card-text>
        	<v-progress-linear v-if="isLoading" indeterminate color="blue"></v-progress-linear>
		<div v-html="migrateResult"></div>
	</v-card-text>
  </v-card>

</div>
`,
    props:{
       name:{type:String, default:''},
    },

    data(){return {
	  isLoading: false,
	  migrateResult: '',
    }},

    mounted(){
    },

    methods: {
	  doExportSQL(){
          this.isLoading = true;
          this.migrateResult = '';
          Swal.showLoading();
          axios({method:"POST",url:"api/export_sql.php", data:{a:1} }).then(response=>{
              this.isLoading = false;
              this.migrateResult = response.data;
              Swal.close();
          }).catch(e=>{
              this.isLoading = false;
              Swal.close();
          });
	  },
 	  doExportCSV(){
          this.migrateResult = 'Еще не готово';
	  },
    },//methods
});
