const URI = '/api/obra/';

export default {
    namespaced: true,

    state: {
        obras: [],
        meta: {}
    },

    mutations: {
        SET_OBRAS(state, obras) {
            state.obras = obras;
        },
        SET_META(state, data) {
            state.meta = data;
        }
    },

    actions: {
        paginate (context, payload = { }){
            axios.get('/api/auth/obras/paginate', { params: payload.params})
                .then(r => r.data)
                .then(data => {
                    context.commit('SET_OBRAS', data.data)
                    context.commit('SET_META', data.meta)
                })
        },

        find(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    });
            });
        },
        global(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + payload.id+'/global', payload.data, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    });
            });
        },
        update(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Actualizar Configuración de Obra",
                    icon: "info",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Actualizar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI + payload.id, payload.data, payload.config)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Configuración actualizada correctamente", {
                                        icon: "success",
                                        timer: 2000,
                                        buttons: false
                                    }).then(() => {
                                        resolve(data);
                                    })
                                })
                                .catch(error => {
                                    reject(error);
                                });
                        } else {
                            resolve();
                        }
                    });
            });
        },

        updateGeneral(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Actualizar Configuración de Obra",
                    icon: "info",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Actualizar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI + payload.id + '/updateGeneral', payload.data, payload.config)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Configuración actualizada correctamente", {
                                        icon: "success",
                                        timer: 2000,
                                        buttons: false
                                    }).then(() => {
                                        resolve(data);
                                    })
                                })
                                .catch(error => {
                                    reject(error);
                                });
                        } else {
                            resolve();
                        }
                    });
            });
        },

        actualizarEstado(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Actualizar Estado de Obra",
                    icon: "info",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Actualizar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI + 'estado/'+payload.id, payload.data, payload.config)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Estado actualizado correctamente", {
                                        icon: "success",
                                        timer: 2000,
                                        buttons: false
                                    }).then(() => {
                                        resolve(data);
                                    })
                                })
                                .catch(error => {
                                    reject(error);
                                });
                        } else {
                            resolve();
                        }
                    });
            });
        },

        actualizarEstadoGeneral(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Actualizar Estado de Obra",
                    icon: "info",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Actualizar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI + 'estadoGeneral/'+payload.id, payload.data, payload.config)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Estado actualizado correctamente", {
                                        icon: "success",
                                        timer: 2000,
                                        buttons: false
                                    }).then(() => {
                                        resolve(data);
                                    })
                                })
                                .catch(error => {
                                    reject(error);
                                });
                        } else {
                            resolve();
                        }
                    });
            });
        },

        getObrasPorUsuario(context, payload = {}) {
            return new Promise((resolve, reject) => {
                axios
                    .get('/api/auth/obras/por-usuario/' + payload.user_id, payload.config)
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    });
            });
        },
    },

    getters: {
        obrasAgrupadas(state) {
            return _.groupBy(state.obras, 'base_datos');
        },
        meta(state) {
            return state.meta;
        }
    }
}
