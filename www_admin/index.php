<?php
require(__DIR__."/../init.php");

try {
    if (!isset($_SERVER["PHP_AUTH_USER"])) {
	Header("WWW-Authenticate: Basic realm=admin_access");
	Header("HTTP/1.0 401 Unauthorized");
	die();
    } else {
        $client_login = $_SERVER["PHP_AUTH_USER"];
        $client_pass =  $_SERVER["PHP_AUTH_PW"];
        $result = $APP->auth->login(["login"=>$client_login, "password"=>$client_pass]);

	if ( $result === false ) {
            Header("WWW-Authenticate: Basic realm=admin_access");
            Header("HTTP/1.0 401 Unauthorized");
            die();
	}
    } // end else PHP_AUTH_USER
} catch(Exception $e) {
    unlink( \MapDapRest\Utils::getFilenameModels() );
    \MapDapRest\Migrate::migrate();
    $APP->auth->login(["login"=>"admin", "password"=>"admin"]);
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">

    <title>APP CONFIG</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@5.8.55/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/font-awesome@4.x/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue-router@3.4.9/dist/vue-router.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="//cdn.jsdelivr.net/npm/sortablejs@1.8.4/Sortable.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/Vue.Draggable/2.20.0/vuedraggable.umd.min.js"></script>
    <script src="js/dbquery.js"></script>
</head>
<body>

  <div id="app">
    <v-app>
        <v-navigation-drawer v-model="leftDrawer" app clipped mobile-breakpoint="0" mini-variant-width="50" overflow width="210" color="primary" dark disable-resize-watcher>
            <v-list dense class="">

                <v-list-item v-for="(item,i) in leftMenu" :to="item.url" link :key="i">
                    <v-list-item-icon>
                        <v-icon>{{item.icon}}</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                        <v-list-item-title>
                            {{item.label}}
                        </v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
    
            </v-list>
        </v-navigation-drawer>
    
        <v-app-bar color="primary" app clipped-left dark dense>
            <v-btn icon @click.stop="">
              <v-icon>fa-bars</v-icon>
            </v-btn>
  
            <v-divider class="mx-1" inset vertical></v-divider>
            <v-toolbar-title class="mx-1">{{title}}</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-divider class="mx-1" inset vertical></v-divider>
  
            <v-menu v-model="admin_menu" :close-on-content-click="false" min-width="300px">
               <template v-slot:activator="{ on }">
                  <v-btn icon v-on="on">
                      <v-icon>settings</v-icon>
                  </v-btn>
               </template>
  
            </v-menu>
        </v-app-bar>
  
        <v-main class="ma-4">
            <router-view></router-view>
        </v-main>
    </v-app>
  </div>

  <script>
    var pages = [];
    var routes = [];
    <?php
        $files = glob(__DIR__."/*.js");
        foreach ($files as $page) {
           $file = basename($page);
           if (substr($file,0,7)=="dialog_") { 
              $name = basename($page, ".js");
              echo $name." = ".file_get_contents($page);
           }
           if (substr($file,0,5)!="page_") continue;
           $src = file_get_contents($page);
           $name = substr(basename($page, ".js"),5);
           echo "pages['".$name."'] = ".$src." \r\n";
           if ($name=="home") echo "routes.push({path:'/', component:pages['".$name."'] }); \r\n";
           echo "routes.push({path:'/$name/:id?', component:pages['".$name."'] }); \r\n";
        }

        $ROOT_URL = str_replace("//", "/", dirname($_SERVER["SCRIPT_NAME"])."/");
        echo "axios.defaults.baseURL = '".$ROOT_URL."'; ";
        echo "axios.defaults.headers.common['token'] = '".$APP->auth->user->token."'; ";
    ?>

    new Vue({
        el: '#app',
        router: new VueRouter({routes}),
        vuetify: new Vuetify({
            icons: {iconfont: 'mdi' || 'md' || 'fa'},
            theme: {
               dark:false,
            },
        }),

        components: {
        },

        data(){return {
            page: null,
            leftDrawer: true,
            title: 'Приложение',
            admin_menu:false,
            leftMenu:[
                       {url:'/home', icon:'mdi-database-sync', label:'Миграция'},
                       {url:'/modules', icon:'mdi-puzzle', label:'Модули'},
                       {url:'/database', icon:'mdi-table-large', label:'База данных'},
                     ],
        }},
       
        async mounted(){
           //const users = Table("users", axios);
           //var user = await users.get();
        },

        methods: {

        }

    })
  </script>


<style>
.v-data-table-header th {font-size:16px !important;}
.v-text-field__details { margin-top:-6px !important; margin-bottom:12px !important; padding: 0px !important;}

</style>
</body>
</html>