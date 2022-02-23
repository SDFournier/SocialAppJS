angular.module('ejercicioRutasApp')
.component('misposts', {
    templateUrl : 'templates/misposts.html',
    bindings    : {},
    controller  : function($http, $auth, $rootScope) {
        
    	 

        var ctrl = this;

		
	 	ctrl.initPosts = function () {
	 		if($rootScope.id_user_posts!=0)
	 		{
	 			$http.get('api/misposts/' + $rootScope.id_user_posts)
			.then(function(response){
				ctrl.posts=response.data;
				console.log(response.data);
			})
			.catch(function(response){
				console.error(response);
				//buscarClase();
				ctrl.posts=[];
			});
	 		}else{
	 			$http.get('api/misposts/' + 0)
			.then(function(response){
				ctrl.posts=response.data;
				console.log(response.data);
			})
			.catch(function(response){
				console.error(response);
				//buscarClase();
				ctrl.posts=[];
			});
	 		}
			
					}

		var initNombreUsuario = function () {
			if($rootScope.id_user_posts!=0)
	 		{
				$http.get('api/usuario/' + $rootScope.id_user_posts)
				.then(function(response){

					/*var names = response.data.map(function(item) {
					  return item['nombre'];
					});
					ctrl.nombreAntes=names;*/
					//ctrl.nombreUsuario=ctrl.nombreAntes.toString();
					ctrl.nombreUsuario=response.data;
					console.log(response.data);
				})
				.catch(function(response){
					console.error(response);
					//buscarClase();
					ctrl.posts=[];
				});
			}else{
				$http.get('api/usuario/' + 0)
				.then(function(response){

					/*var names = response.data.map(function(item) {
					  return item['nombre'];
					});
					ctrl.nombreAntes=names;*/
					//ctrl.nombreUsuario=ctrl.nombreAntes.toString();
					ctrl.nombreUsuario=response.data;
					console.log(response.data);
				})
				.catch(function(response){
					console.error(response);
					//buscarClase();
					ctrl.posts=[];
				});
			}
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
		this.$onInit = function() {
			ctrl.objeto=[];
			ctrl.initPosts();
			initNombreUsuario();
			ctrl.saberquienSoy();
			$rootScope.id_user_posts=0;
			ctrl.nuevoPost = null;
			ctrl.cargadoImagen=null;
			ctrl.posts=[];
			ctrl.nombreUsuario=[];
			console.log($rootScope.id_user_posts);

			
		
		}
		ctrl.guardarPost = function () {
				
			 // Nuevo
		 		

				
				$http.post('api/post', ctrl.nuevoPost)
					.then(function (response) {
						ctrl.initPosts();
						ctrl.nuevaPost = null;
						ctrl.cargadoPost=null;
					})
					.catch(function (response) {
						console.error(response);
					});
			
	    }   


	    ctrl.borrarPost= function (id) {          
								
				if (confirm('¿Estás seguro de querer borrar este post?')) {
				$http.delete('api/post/' + id)
					.then(function (response) {
						ctrl.initPosts();
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


	    ctrl.sonMisPosts= function (){
	    	console.log("id_dueno:"+ctrl.posts+" y el id del logeado:"+ctrl.quienSoy);
	    	if(ctrl.posts==ctrl.quienSoy){
			return true	}else{return false}
    	}

    
}
})
