angular.module('ejercicioRutasApp')
.component('amigos', {
	templateUrl	: 'templates/amigos.html',
	controller	: function ($http, $auth, $location, $rootScope) {
		var ctrl = this;

		var initAmigos = function () {
			$http.get('api/amigos')
				.then(function (response) {
					ctrl.amigos = response.data;
					console.log(response.data);
				})
				.catch(function (response) {
					console.error(response);
				});
		}

		ctrl.eliminar = function (id) {
			if (confirm('¿Estás seguro de querer borrar este contacto?')) {
				$http.delete('api/amigos/' + id)
					.then(function (response) {
						initAmigos();
						console.log(response);
					})
					.catch(function (response) {
						if (response.status==404) {
							alert('No se encontró el contacto');
						} else {
							console.error(response);
						}
					});
			}
		}

		ctrl.descompartir = function (id) {
			$http.delete('api/compartir/' + id)
				.then(function (response) {
					initContactos();
				})
				.catch(function (response) {
					if (response.status==404) {
						alert('No se encontró el contacto');
					} else {
						console.error(response);
					}
				});
		}

		ctrl.compartir = function (id) {
			$http.patch('api/compartir/' + id, {})
				.then(function (response) {
					initContactos();
				})
				.catch(function (response) {
					if (response.status==404) {
						alert('No se encontró el contacto');
					} else {
						console.error(response);
					}
				});
		}

		ctrl.haceralgo = function (){
			$http.post('api/tabla', ctrl.nuevoContacto)
						.then(function (response) {
							console.log(response.data);
							initAmigos();
							ctrl.nuevoContacto = {};
							
						})
						.catch(function (response) {
						if (response.status==404) {
							alert('No se encontró el contacto');
						} else {
							console.error(response);
						}
						});

		}

		ctrl.guardar = function () {

			console.log(ctrl.nuevoContacto);
					$http.post('api/amigos', ctrl.nuevoContacto)
						.then(function (response) {
							console.log(response.data);
							initAmigos();
							ctrl.nuevoContacto = {};
							ctrl.ocultar=null;
							
						})
						.catch(function (response) {
						if (response.status==404) {
							alert('No se encontró el contacto');
						} else {
							console.error(response);
						}
						});
		}
		

		ctrl.editar = function (id) {
			$http.get('api/contactos/'+id)
				.then(function (response) {
					response.data.fecha_de_nacimiento = response.data.fecha_de_nacimiento ? new Date(response.data.fecha_de_nacimiento) : null;
					ctrl.nuevoContacto = response.data;
				})
				.catch(function (response) {
					if (response.status==404) {
						alert('No se encontró el contacto con id='+id);
					} else {
						console.error(response);
					}
				});
		}
		ctrl.irAlPerfil = function (id){

			
			$rootScope.idPerfil= id;
			$location.path('/perfilcontacto');


		}


		this.$onInit = function () {
			initAmigos();
			ctrl.nuevoContacto = {};
			ctrl.ocultar=null;
		}
	}
})
;