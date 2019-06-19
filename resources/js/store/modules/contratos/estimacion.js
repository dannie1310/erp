const URI = '/api/contratos/estimacion/';

export default {
    namespaced: true,
    state: {
        estimaciones: [],
        meta: {}
    },

    mutations: {
        SET_ESTIMACIONES(state, data) {
            state.estimaciones = data
        },

        SET_META(state, data) {
            state.meta = data
        },

        APROBAR_ESTIMACION(state, id) {
            state.estimaciones.forEach(estimacion => {
                if(estimacion.id == id) {
                    estimacion.estado = 1;
                }
            })
        },

        REVERTIR_APROBACION(state, id) {
            console.log('entro');
            state.estimaciones.forEach(estimacion => {
                if(estimacion.id == id) {
                    console.log('encontrata')
                    estimacion.estado = 0;
                }
            })
        }
    },

    actions: {
        pdf(context, payload) {
            axios.get(URI + payload+'/formato-orden-pago', {params: payload.params})
                .then((data) => {

                });
        },

        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Estimación",
                    text: "¿Estás seguro/a de que la información es correcta?",
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
                                    swal("Estimación registrada correctamente", {
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

        getConceptos(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id + '/getConceptos')
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

        aprobar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Estás seguro?",
                    text: "Aprobar Estimación",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Aprobar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .patch(URI + payload.id + '/aprobar')
                                .then(r => r.data)
                                .then(data => {
                                    swal("Estimación aprobada correctamente", {
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
                        } else {
                            reject();
                        }
                    });
            });
        },

        revertirAprobacion(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Estás seguro?",
                    text: "Revertir Aprobación",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Revertir',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .patch(URI + payload.id + '/revertirAprobacion')
                                .then(r => r.data)
                                .then(data => {
                                    swal("Aprobación revertida correctamente", {
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
                        } else {
                            reject();
                        }
                    });
            });
        }
    },
    getters: {
        estimaciones(state) {
            return state.estimaciones
        },

        meta(state) {
            return state.meta
        }
    }
}