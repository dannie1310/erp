const URI = '/api/contabilidad/cierre-periodo/';

export default {
    namespaced: true,
    state: {
        cierres: [],
        currentCierre: null,
        meta: {}
    },

    mutations: {
        SET_CIERRES(state, data) {
            state.cierres = data
        },

        SET_META(state, data) {
            state.meta = data
        },

        SET_CIERRE(state, data) {
            state.currentCierre = data
        },

        UPDATE_CIERRE(state, data) {
            state.cierres = state.cierres.map(cierre => {
                if (cierre.id === data.id) {
                    return Object.assign([], cierre, data)
                }
                return cierre
            })
            state.currentCierre = data
        },

        UPDATE_ATTRIBUTE(state, data) {
            state.currentCierre[data.attribute] = data.value
        }
    },

    actions: {
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

        find(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
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
                    title: "Registrar Cierre de Periodo",
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
                                    swal({
                                        title: "Cierre de periodo registrado correctamente",
                                        text: " ",
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

        abrir(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Por favor escriba un motivo de apertura",
                    content: 'input',
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Abrir',
                            closeModal: false,
                        }
                    },
                    dangerMode: true
                })
                    .then(value => {
                        return new Promise((resolve, reject) => {
                           if (value.length === 0) {
                               swal.stopLoading();
                               swal.close();
                               swal("", "El motivo de apertura es obligatorio", "error")
                                   .then(() => {
                                       context.dispatch('abrir',payload);
                                   })
                           } else {
                               resolve(value);
                           }
                        })
                    })
                    .then(value => {
                        axios
                            .patch(URI + payload.id + '/abrir', {
                                motivo: value
                            })
                            .then(r => r.data)
                            .then(data => {
                                swal("Periodo abierto correctamente", {
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
                    })
            })
        },

        cerrar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Cerrar Periodo",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Cerrar',
                            closeModal: false,
                        }
                    },
                    dangerMode: true
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .patch(URI + payload.id + '/cerrar')
                                .then(r => r.data)
                                .then(data => {
                                    swal("Periodo cerrado correctamente", {
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
        }
    },

    getters: {
        cierres(state) {
            return state.cierres
        },

        meta(state) {
            return state.meta
        },

        currentCierre(state) {
            return state.currentCierre
        }
    }
}