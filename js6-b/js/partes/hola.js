angular.module('ejercicioRutasApp')
.component('hola', {
	templateUrl	: 'templates/hola.html',
	controller	: function ($auth, $location, $http, $rootScope) {

		var ctrl = this;


ctrl.mostrar = function () {

	if ($auth.isAuthenticated())
	{
		
		let btn = document.querySelector("#btn");
		let sidebar = document.querySelector(".sidebar");
		let searchBtn = document.querySelector(".bx-search");
		

		btn.onclick= function(){
			if(ctrl.autenticado){
				sidebar.classList.toggle("open");
				
	    	}
	        
	        
	    }

	}else{
		
	}                
		
	}

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
		return $location.path() == ruta;
	}

	ctrl.mostrarMisPosts= function(ruta){
		if($location.path()===ruta){
			window.location.reload(true);
		}else{
			$location.path('/misposts');
		}
		

	}
	ctrl.cargarPerfil= function (){
		$http.get('api/datosperfil')
				.then(function (response) {
					ctrl.datosPerfil = response.data;
				})
				.catch(function (response) {
					console.error(response);
				});
	}

	this.$onInit = function () {
		ctrl.autenticado = $auth.isAuthenticated;
		

		ctrl.palabra="";

	}
		
}
	
});