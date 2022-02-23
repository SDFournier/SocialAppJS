<!doctype html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Red Social</title>

        <!-- ===== Boxicons CSS ===== -->
        <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>

        <link rel="stylesheet" type="text/css" href="css/style.css">

        <!-- Bootstrap core CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
        <!-- Bootstrap icons CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css" integrity="sha512-xnP2tOaCJnzp2d2IqKFcxuOiVCbuessxM6wuiolT9eeEJCyy0Vhcwa4zQvdrZNVqlqaxXhHqsSV1Ww7T2jSCUQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
		<!-- AngularJS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.8.2/angular.min.js" integrity="sha512-7oYXeK0OxTFxndh0erL8FsjGvrl2VMDor6fVqzlLGfwOQQqTbYsGPv4ZZ15QHfSk80doyaM0ZJdvkyDcVO7KFA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <!-- Router -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/angular-route/1.8.2/angular-route.min.js" integrity="sha512-5zOAub3cIpqklnKmM05spv4xttemFDlbBrmRexWiP0aWV8dlayEGciapAjBQWA7lgQsxPY6ay0oIUVtY/pivXA==" crossorigin="anonymous"></script>
        <!-- Satellizer -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/satellizer/0.14.1/satellizer.min.js" integrity="sha512-ZLAGfaREnf5hq51URaG84dBY6DhCVOxxwvhMEsPPRj5qTbN9NU2cp4hyaWzc9a0k1UrsLYWU5vbVPvme0d/n6A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>


        <style>
            .form-signin {
              width: 100%;
              max-width: 330px;
              padding: 15px;
              margin: auto;
            }
            .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            }
            @media (min-width: 768px) {
            .bd-placeholder-img-lg {
            font-size: 3.5rem;
            }
            }
            .container {
            max-width: 960px;
            }
            .lh-condensed {
            line-height: 1.25; 
            }
            
}
        </style>
    </head>
    <body class="bg-light" data-ng-app="ejercicioRutasApp">
 
        <hola></hola>
         <section class="homecuerpo">
              <div class="container">
                    <div class="letratitulo">
                        <h1>RedSocial</h1>
                        <p class="lead"></p>
                    </div>
                    <ng-view></ng-view>
                </div>
            
            
          </section>
          
        
        <br>
        <script src="js/app.js"></script><!-- incluir primero -->
        
        <script src="js/partes/hola.js"></script>
        <script src="js/rutas/posts.js"></script>
        <script src="js/rutas/acercade.js"></script>
        <script src="js/rutas/misposts.js"></script>
        <script src="js/rutas/configuracion.js"></script>
        <script src="js/rutas/registro.js"></script>
        <script src="js/rutas/galeria.js"></script>
        <script src="js/rutas/mensajes.js"></script>
        <script src="js/rutas/perfilcontacto.js"></script>
        <script src="js/rutas/amigos.js"></script>
        <script src="js/rutas/login.js"></script>
        <script src="js/rutas/controlarposts.js"></script>
        <script src="js/rutas/controlarmensajes.js"></script>
        <script type="text/javascript">
         
            </script>
    </body>
</html>
