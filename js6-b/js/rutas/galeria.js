angular.module('ejercicioRutasApp')
.component('galeria', {
    templateUrl : 'templates/galeria.html',
    bindings    : {},
    controller  : function($http, $auth) {
        

            var ctrl = this;
		 var initImagenes = function () {
			$http.get('api/imagenes')
			.then(function(response){
				ctrl.imagenes=response.data;
				console.log(response.data);
			})
			.catch(function(response){
				console.error(respuesta);
				//buscarClase();
				ctrl.imagenes=[];
			});
		}
        
		this.$onInit = function() {
			initImagenes();
			ctrl.nuevaImagen = null;
			ctrl.cargadoImagen=null;
			ctrl.imagenes=[];


		
		}
		ctrl.guardarImagen = function () {
				
			 // Nuevo
		 		

				
				$http.post('api/imagen', ctrl.nuevaImagen)
					.then(function (response) {
						ctrl.$onInit();
						ctrl.nuevaImagen = null;
						ctrl.cargadoImagen=null;
					})
					.catch(function (response) {
						console.error(respuesta);
					});
			
	    }   

	    ctrl.borrarImagen= function (id) {
								
				if (confirm('¿Estás seguro de querer borrar esta imagen?')) {
				$http.delete('api/imagen/' + id)
					.then(function (response) {
						initImagenes();
					})
					.catch(function (response) {
						if (response.status==404) {
							alert('No se encontró la imagen');
						} else {
							console.error(response);
						}
					});
			}
			
	    }   
}
})
