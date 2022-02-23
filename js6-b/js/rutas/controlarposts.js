angular.module('ejercicioRutasApp')
.component('controlarposts', {
    templateUrl : 'templates/controlarposts.html',
    bindings    : {},
    controller  : function($http, $auth, $location, $rootScope) {
        
    	
        var ctrl = this;

	 	var initPosts = function () {
			$http.get('api/posts')
			.then(function(response){
				ctrl.posts=response.data;
				console.log(response.data);
			})
			.catch(function(response){
				console.error(respuesta);
				//buscarClase();
				ctrl.posts=[];
			});
		}
        
		this.$onInit = function() {
			initPosts();
			ctrl.nuevoPost = null;
			ctrl.cargadoImagen=null;
			ctrl.posts=[];


		
		}
		

	    ctrl.borrarPost= function (id) {
								
				if (confirm('¿Estás seguro de querer borrar este post?')) {
				$http.delete('api/post/' + id)
					.then(function (response) {
						initPosts();
						console.log(response.data);
					})
					.catch(function (response) {
						if (response.status==404) {
							alert('No se encontró el post');
						} else {
							console.error(response);
						}
					});
			}
			
	    }  
	    ctrl.redirigirAPostAmigo = function(id){
	    	console.log(id);
	    	$rootScope.id_user_posts=id;
	    	$location.path('/misposts'); 
	    } 
}
})
