angular.module('ejercicioRutasApp')
.component('contactos', {
	templateUrl	: 'templates/contactos.html',
	controller	: function ($http, $auth, $location, $rootScope) {
		var ctrl = this;

		var initContactos = function () {
			$http.get('api/contactos')
				.then(function (response) {
					ctrl.contactos = response.data;
				})
				.catch(function (response) {
					console.error(response);
				});
		}

		ctrl.borrar = function (id) {
			if (confirm('¿Estás seguro de querer borrar este contacto?')) {
				$http.delete('api/contactos/' + id)
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

		ctrl.guardar = function () {
			if (ctrl.nuevoContacto!=null) {
				if (ctrl.nuevoContacto.id!=null) { // Edición
					$http.patch('api/contactos/' + ctrl.nuevoContacto.id, ctrl.nuevoContacto)
						.then(function (response) {
							initContactos();
							ctrl.nuevoContacto = null;
						})
						.catch(function (response) {
							if (response.status==404) {
								alert('No se encontró el contacto con id=' + ctrl.nuevoContacto.id);
							} else {
								console.error(response);
							}
						});
				} else { // Nuevo
					$http.post('api/contactos', ctrl.nuevoContacto)
						.then(function (response) {
							initContactos();
							ctrl.nuevoContacto = null;
						})
						.catch(function (response) {
							console.error(response);
						});
				}
			}
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
			initContactos();
			ctrl.nuevoContacto = null;
		}
	}
})
;