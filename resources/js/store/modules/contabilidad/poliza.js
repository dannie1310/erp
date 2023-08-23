const URI = '/api/contabilidad/poliza/';
export default {
    namespaced: true,
    state: {
        polizas: [],
        currentPoliza: null,
        meta: {},
    },

    mutations: {
        SET_POLIZAS(state, data) {
            state.polizas = data;
        },

        SET_META(state, data) {
            state.meta = data;
        },

        UPDATE_POLIZA(state, data) {
            state.polizas = state.polizas.map(poliza => {
                if (poliza.id === data.id) {
                    return Object.assign({}, poliza, data)
                }
                return poliza
            })
            state.currentPoliza = state.currentPoliza ? data : null;
        },

        SET_POLIZA(state, data) {
            state.currentPoliza = data;
        }
    },

    actions: {
        paginate (context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'paginate', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            })
        },

        find(context, payload) {
            return new Promise((resolve, reject) => {
                axios.get(URI + payload.id, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },

        findEdit(context, payload) {
            return new Promise((resolve, reject) => {
                axios.get(URI + payload.id + '/editar', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },

        update(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Guardar cambios de la Prepóliza",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Guardar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .patch(URI + payload.id, payload.data, { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Prepóliza Actualizada correctamente", {
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

        validar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Validar Prepóliza",
                    text: "¿Esta seguro de que deseas validar la Prepóliza?",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Validar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios.patch(URI + payload.id + '/validar', payload.data, { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Prepóliza Validada correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    }).then(() => {
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

        omitir(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Omitir Prepóliza",
                    text: "¿Esta seguro de que deseas omitir la Prepóliza?",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Omitir',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios.patch(URI + payload.id + '/omitir', payload.data,  { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Prepóliza omitida correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    }).then(() => {
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

        asociarCFDI(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Asociar CFDI a Pólizas",
                    text: "¿Está seguro de asociar los CFDI?",
                    icon: "info",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Asociar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI+"asociar", payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("CFDI asociados.", {
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

        getPolizasPorAsociar(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI+'polizasCFDI', { params: payload.params })
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
        polizas(state) {
            return state.polizas
        },

        meta(state) {
            return state.meta
        },

        currentPoliza(state) {
            return state.currentPoliza
        }
    }
}
