const URI = '/api/finanzas/pago/';

export default {
    namespaced: true,
    state: {
        pagos: [],
        currentPago: null,
        meta: {}
    },

    mutations: {
        SET_PAGOS(state, data) {
            state.pagos = data;
        },

        SET_PAGO(state, data) {
            state.currentPago = data;
        },
        UPDATE_FONDO(state, data){
            state.pagos= state.pagos.map(pago => {
                if(pago.id === data.id){
                    return Object.assign({}, pago, data)
                }
                return pago
            })
            state.currentPago = data ;
        },

        SET_META(state, data) {
            state.meta = data;
        },
        UPDATE_ATTRIBUTE(state, data) {
            _.set(state.currentPago, data.attribute, data.value);
        },
        DELETE_PAGO(state, id) {
            state.pagos = state.pagos.filter(pago => {
                return pago.id != id
            });
        }
    },

    actions: {
        index(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    });
            });
        },
        paginate(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'paginate', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
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
        eliminar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Eliminar el pago",
                    text: "¿Está seguro de que desea eliminar este pago?",
                    icon: "warning",
                    closeOnClickOutside: false,
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Eliminar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .delete(URI + payload.id, {params: payload.params})
                                .then(r => r.data)
                                .then(data => {
                                    swal("Pago eliminado correctamente", {
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
                        } else {
                            reject();
                        }
                    });
            });
        },
        documentosParaPagar(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'documentosParaPagar', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    });
            });
        },
        documentoParaPagar (context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id+'/documentoParaPagar', { params: payload.params })
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Pago",
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
                    }
                })
                .then((value) => {
                    if (value) {
                        axios
                            .post(URI, payload)
                            .then(r => r.data)
                            .then(data => {
                                swal("Pago registrado correctamente", {
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
        aplicarPago(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Aplicar Pago Manual",
                    text: "¿Está seguro de que la información es correcta?",
                    icon: "info",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Aplicar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI + 'aplicarPago', payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Aplicación de Pago registrado correctamente", {
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
                        }
                    });
            });
        },
        porConciliar(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI+'porConciliar', payload)
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    });
            });
        },
        conciliar(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI+'conciliar', payload)
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    });
            });
        }
    },

    getters: {
        pagos(state) {
            return state.pagos;
        },

        meta(state) {
            return state.meta;
        },

        currentPago(state) {
            return state.currentPago;
        }
    }
}
