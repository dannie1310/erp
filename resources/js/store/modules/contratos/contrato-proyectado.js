const URI = '/api/contratos/contrato-proyectado/';

export default {
    namespaced: true,
    state: {
        contratoProyectado: [],
        currentContratos: null,
        meta: {}
    },

    mutations: {
        SET_CONTRATO_PROYECTADO(state, data) {
            state.contratoProyectado = data
        },

        SET_CONTRATO_PROYECTADOS(state, data) {
            state.currentContratos = data
        },

        SET_META(state, data) {
            state.meta = data
        },
        DELETE_CONTRATO_PROYECTADO(state, id) {
            state.contratoProyectado = state.contratoProyectado.filter((cp) => {
                return cp.id !== id;
            })
            if (state.currentContratos && state.currentContratos.id === id) {
                state.currentContratos = null;
            }
        },


        UPDATE_CONTRATO_PROYECTADOS(state, data) {
            state.contratoProyectado = state.contratoProyectado.map(contrato => {
                if (contrato.id === data.id) {
                    return Object.assign({}, contrato, data)
                }
                return contrato
            })
            state.currentContratos != null ? data : null;
        }
    },

    actions: {
        cargarLayout(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'layout', payload.data, payload.config)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
       paginate (context, payload){
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'paginate', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },
        update(context, payload){
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Estás seguro?",
                    text: "Actualizar Contrato Proyectado",
                    icon: "warning",
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
                                .patch(URI + payload.id, payload.data)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Contrato Proyectado actualizado correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    })
                                        .then(() => {
                                            resolve(data);
                                        })
                                })
                                .catch(error => {
                                    reject(error);
                                })
                        }
                    });
            });
        },
        find (context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id, { params: payload.params })
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        store(context,payload){
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Contrato Proyectado",
                    text: "¿Está seguro de que la información es correcta?",
                    icon: "info",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Registrar',
                            closeModal: false,
                        }
                    }                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI, payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Contrato proyectado registrado correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    }).then(() => {
                                        resolve(data);
                                    })
                                })
                                .catch(error => {
                                    reject(error);
                                });
                        }
                    });
            });

        },

        getArea(payload = {}) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'getArea', { params: payload.params })
                    .then(r => r.data)
                    .then((data) => {

                        resolve(data.data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        actualiza(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Actualizar Contrato Proyectado",
                    icon: "warning",
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
                                .patch(URI + payload.id + '/actualizar', payload.data, { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Contrato Proyectado actualizado correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    }).then(() => {
                                            context.commit('UPDATE_CONTRATO_PROYECTADOS',data);
                                            resolve(data);
                                        })
                                })
                                .catch(error => {
                                    reject(error);
                                })
                        } else {
                            reject();
                        }
                    });
            });
        },
    },
    getters: {
        contratoProyectado(state) {
            return state.contratoProyectado
        },

        meta(state) {
            return state.meta
        },
        currentContratos(state) {
            return state.currentContratos
        }
    }
}
