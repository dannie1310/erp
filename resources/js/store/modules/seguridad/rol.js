const URI = '/api/SEGURIDAD_ERP/rol/';

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
        index(context, payload = {}) {
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

        asignacionMasiva(context, payload = {}) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Asignar Roles",
                    text: "¿Estás seguro/a de que la información es correcta?",
                    icon: "info",
                    closeOnClickOutside: false,
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                        },
                        confirm: {
                            text: 'Si, Asignar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI + 'asignacion-masiva', payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("La asignación de roles ha sido aplicada exitosamente", {
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