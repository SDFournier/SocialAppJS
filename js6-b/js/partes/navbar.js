angular.module('ejercicioRutasApp')
.component('navbar', {
	templateUrl	: 'templates/navbar.html',
	controller	: function ($auth, $location, $http) {
		var ctrl = this;

		ctrl.salir = function () {
			if (confirm ('¿Está seguro de querer salir del sistema?')) {
				$http.post('api/logout')
					.then(function (response) {
						$auth.logout();
						$location.path('/login');
					})
					.catch(function (response) {
						alert('Imposible salir del sistema');
					})
			}
		}

		ctrl.actual = function (ruta) {
			
		}

		this.$onInit = function () {
			ctrl.autenticado = $auth.isAuthenticated;
			
		}
	}
})
;