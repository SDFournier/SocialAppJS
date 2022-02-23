angular.module('ejercicioRutasApp', ['ngRoute', 'satellizer'])
.config(function($authProvider, $httpProvider) {
	var rutaRelativa = window.location.pathname.substr(0,window.location.pathname.lastIndexOf('/'))+'/'; 
	$authProvider.baseUrl = rutaRelativa;
	$authProvider.loginUrl = 'api/login';
	$authProvider.tokenName = 'jwt';
	
	ctrl=this;

	$httpProvider.interceptors.push(function($q, $window, $rootScope) {
		$rootScope.id_user_posts=0;
		return {
			'responseError': function (rejection) {
				if(rejection.status === 401) {
					$rootScope.logout();
					$window.location.href = rutaRelativa;
				}
				return $q.reject(rejection);
			}
		};
	});


})
.config(function($routeProvider) { 
	$routeProvider
		.when("/", {
			template: '<login></login>',
			resolve: {
				necesitaLogin: saltarSiLogueado
			},
		})
		.when("/login", {
			template: '<login></login>',
			resolve: {
				necesitaLogin: saltarSiLogueado
			},
		})
		.when("/posts", {
			template: '<posts></posts>',
			resolve: {
				necesitaLogin: loginRequerido
			},
		})
		.when("/amigos", {
			template: '<amigos></amigos>',
			resolve: {
				necesitaLogin: loginRequerido
			},
		})
		.when("/misposts", {
			template: '<misposts></misposts>',
			resolve: {
				necesitaLogin: loginRequerido
			},
		})
		.when("/acercade", {
			template: '<acercade></acercade>',
			resolve: {
				necesitaLogin: loginRequerido
			},
		})
		.when("/configuracion", {
			template: '<configuracion></configuracion>',
			resolve: {
				necesitaLogin: loginRequerido
			},
		})
		.when("/controlarposts", {
			template: '<controlarposts></controlarposts>',
			resolve: {
				necesitaLogin: loginRequerido
			},
		})
		.when("/controlarmensajes", {
			template: '<controlarmensajes></controlarmensajes>',
			resolve: {
				necesitaLogin: loginRequerido
			},
		})
		.when("/galeria", {
			template: '<galeria></galeria>',
			resolve: {
				necesitaLogin: loginRequerido
			},
		})
		.when("/perfilcontacto", {
			template: '<perfilcontacto></perfilcontacto>',
			resolve: {
				necesitaLogin: loginRequerido
			},
		})
		.when("/mensajes", {
			template: '<mensajes></mensajes>',
			resolve: {
				necesitaLogin: loginRequerido
			},
		})
		.when("/404", {
			templateUrl: '404.html',
		})
		.when("/registro", {
			template: '<registro></registro>',
			resolve: {
				necesitaLogin: saltarSiLogueado
			},
		})
		.otherwise('/404');
		;

		function saltarSiLogueado($q, $auth, $location) {
			var diferido = $q.defer();
			if ($auth.isAuthenticated()) {
				$location.path('/posts');
				diferido.reject(); /* Rechazo - Detener */
			} else {
				diferido.resolve();  /* Resolver - Continuar */
			}
			return diferido.promise; /* Promesa */
	    };

	    function loginRequerido($q, $auth, $location) {
			var diferido = $q.defer();
			if ($auth.isAuthenticated()) {
				diferido.resolve();
			} else {
				$location.path('/login');
				diferido.reject();
			}
			return diferido.promise;
		}
		function esAdmin($q, $auth, $location) {
			var diferido = $q.defer();
			if ($auth.isAuthenticated()&&ctrl.saberquienSoy==99) {
				
				diferido.resolve();
			
			} else {
				$location.path('/login');
				diferido.reject();
			}
			return diferido.promise;
		}
})
;