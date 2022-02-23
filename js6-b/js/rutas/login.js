angular.module('ejercicioRutasApp')
.component('login', {
	templateUrl	: 'templates/login.html',
	controller	: function ($auth, $location,$rootScope) {
		var ctrl = this;

		ctrl.ingresar = function () {
			$auth.login({"email": ctrl.login.email, "clave": ctrl.login.clave }) 
				.then(function(response) {
					console.log(response.data);
					$auth.setToken(response.data.jwt);
					$location.path('/posts');
					$rootScope.cargarNavbar;
				})
				.catch(function(response) {
					alert("Login incorrecto");
					ctrl.login = {};
				});
		}

		this.$onInit = function () {
			ctrl.login = {};
		}
	}
})
;