angular.module('ejercicioRutasApp')
.component('configuracion', {
	templateUrl	: 'templates/configuracion.html',
	controller	: function ($http, $auth, $location) {
		var ctrl = this;

		ctrl.guardar = function () {
			$http.patch('api/perfil/' + $auth.getPayload().id, ctrl.perfil)
				.then(function (response) {
					alert('La clave fue actualizada exitosamente');
					$location.path('/contactos');
				})
				.catch(function (response) {
					alert('No se pudo actualizar la clave del usuario');
				});
		}
		ctrl.guardarImagen = function() {
			console.log(ctrl.nuevaImagenPerfil);
			$http.patch('api/foto', ctrl.nuevaImagenPerfil)
				.then(function (response) {
					alert('La foto fue actualizada.');
					$location.path('/posts');
				})
				.catch(function (response) {
					alert('No se pudo actualizar la foto del usuario');
				});

		}

		this.$onInit = function () {
			ctrl.perfil = {};
			ctrl.nuevaImagenPerfil={};
			ctrl.saberquienSoy();
			console.log($auth.getPayload());
		}

		ctrl.saberquienSoy= function(){
        	$http.get('api/quiensoy' )
				.then(function(response){

					
					ctrl.quienSoy=response.data;
					console.log("quien soy: "+response.data);
				})
				.catch(function(response){
					console.error(response);
					//buscarClase();
					ctrl.posts=[];
				});
        }

        ctrl.controlarPosts = function(){
        	$location.path('/controlarposts');
        }
        ctrl.controlarMensajes = function(){
        	$location.path('/controlarmensajes');
        }
	}
})
;