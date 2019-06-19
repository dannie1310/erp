const URI = '/api/CONFIGURACION/area-subcontratante/';

export default {
    namespaced: true,

    state: {
        roles: []
    },

    mutations: {
        SET_ROLES(state, data) {
            state.roles = data;
        }
    },

    actions: {
        index(payload = {}) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI, { params: payload.params })
                    .then(r => r.data)
                    .then((data) => {

                        resolve(data.data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },

        find(payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id)
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },

        getAreasUsuario(context,user_id) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'por-usuario/' + user_id)
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    });
            });
        },

        asignacionAreasSubcontratantes(context, payload = {}) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Aplicar Cambios",
                    text: "¿Estás seguro/a de que la información es correcta?",
                    icon: "info",
                    closeOnClickOutside: false,
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Continuar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI + 'asignacion-areas-subcontratantes', payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("La acción ha sido aplicada con éxito", {
                                        icon: "success",
                                        timer: 2000,
                                        buttons: false
                                    }).then(() => {
                                        resolve(data);
                                    })
                                })
                                .catch(error =>  {
                                    reject(error);
                                });
                        }else{
                            reject();

                        }
                    });
            });
        }
    },

    getters: {
        roles(state) {
            return state.roles
        }
    }
}