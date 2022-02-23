angular.module('ejercicioRutasApp')
.component('perfilcontacto', {
    templateUrl : 'templates/perfilcontacto.html',
    bindings    : {},
    controller  : function($http, $auth, $rootScope) {

        var ctrl = this;

        

        this.$onInit = function () {
            console.log("hola");
            console.log($rootScope.idPerfil);
            ctrl.perfil = {};
            
        }
        
	}
})
