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

        find(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id, payload.config)
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },

        getRolesUsuario(context, payload = {}) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'por-usuario/' + payload.user_id, payload)
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    });
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
                            visible: true
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
                        reject();
                    });
            });
        },

        asignarPermisos(context, payload = {}) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Asignar Permisos",
                    text: "¿Estás seguro/a de que la información es correcta?",
                    icon: "info",
                    closeOnClickOutside: false,
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
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
                                .post(URI + 'asignacion-permisos', payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("La asignación de permisos ha sido aplicada exitosamente", {
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
                        reject();
                    });
            });
        },

        desasignacionMasiva(context, payload = {}) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Desasignar Roles",
                    text: "¿Estás seguro/a de que la información es correcta?",
                    icon: "info",
                    closeOnClickOutside: false,
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Desasignar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI + 'desasignacion-masiva', payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("La desasignación de roles ha sido aplicada exitosamente", {
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
        },

        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Crear Rol",
                    text: "¿Estás seguro/a de que la información es correcta?",
                    icon: "info",
                    buttons: ['No, cancelar',
                        {
                            text: "Si, Crear",
                            closeModal: false,
                        }
                    ]
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI, payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal({
                                        title: "Rol creado exitosamente",
                                        text: " ",
                                        icon: "success",
                                        timer: 3000,
                                        buttons: false
                                    }).then(() => {
                                        resolve(data);
                                    })
                                })
                                .catch(error => {
                                    reject(error);
                                });
                        }
                        reject()
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