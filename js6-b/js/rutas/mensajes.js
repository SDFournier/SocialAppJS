angular.module('ejercicioRutasApp')
.component('mensajes', {
    templateUrl : 'templates/mensajes.html',
    bindings    : {},
    controller  : function($http, $auth) {
        var ctrl = this;

        var initMensajes = function () {
            $http.get('api/mensajes')
                .then(function (response) {
                    ctrl.mensajes = response.data;
                    console.log(response);
                })
                .catch(function (response) {
                    console.error(respuesta);
                    //buscarClase();
                    ctrl.mensajes=[];
                });

        }
        var initMensajesRecibidos = function () {
            $http.get('api/mensajesrecibidos')
                .then(function (response) {
                    ctrl.mensajesRecibidos = response.data;
                    console.log(response);
                })
                .catch(function (response) {
                    console.error(respuesta);
                    //buscarClase();
                    ctrl.mensajesRecibidos=[];
                });

        }

        this.$onInit = function () {
            ctrl.mensajes=[];
            ctrl.mensajesRecibidos=[]
            ctrl.nuevoMensaje = null;
            ctrl.contactos=[];
            initMensajes();
            initMensajesRecibidos();

            

            $http.get('api/amigos')
                .then(function (response) {
                    ctrl.contactos = response.data;
                    console.log(response);
                })
                .catch(function (response) {
                    console.error(response);
                });
        }

        ctrl.editarMensaje = function () {

        }
        ctrl.enviarMensaje = function () {
             $http.post('api/mensaje', ctrl.nuevoMensaje)
                        .then(function (response) {
                            ctrl.$onInit();
                            ctrl.nuevoMensaje = null;
                        })
                        .catch(function (response) {
                            console.error(response.data);
                        });
        }

        ctrl.borrarMensaje = function (id) {
            if (confirm('¿Estás seguro de querer borrar este mensaje?')) {
                $http.delete('api/mensaje/' + id)
                    .then(function (response) {
                        initMensajes();
                    })
                    .catch(function (response) {
                        if (response.status==404) {
                            alert('No se encontró el mensaje');
                        } else {
                            console.error(response);
                        }
                    });
            }
        }
        
}
})


