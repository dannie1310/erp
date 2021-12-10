const URI = '/api/portal-proveedor/solicitud-autorizacion-avance/';

export default {
    namespaced: true,
    state: {
        estimaciones: [],
        currentEstimacion: null,
        meta: {},
    },

    mutations: {
        SET_ESTIMACIONES(state, data) {
            state.estimaciones = data
        },

        SET_ESTIMACION(state, data) {
            state.currentEstimacion = data;
        },

        SET_META(state, data) {
            state.meta = data
        },

        UPDATE_ATTRIBUTE(state, data) {
            _.set(state.currentEstimacion, data.attribute, data.value);
        },
    },

    actions: {
        index(context, payload){
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'index', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },
        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Estimación",
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
        ordenarConceptos (context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + payload.id+'/ordenarConceptos', payload)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        update(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Actualizar la Estimación",
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
                                .patch(URI + payload.id, payload.data,{ params: payload.params } )
                                .then(r => r.data)
                                .then(data => {
                                    swal("Estimación actualizada correctamente", {
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
        eliminar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Eliminar la Estimación",
                    text: "¿Está seguro de que desea eliminar esta estimación?",
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
                                .patch(URI + payload.id+'/eliminar',payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Estimación eliminada correctamente", {
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
    },
    getters: {
        estimaciones(state) {
            return state.estimaciones
        },

        meta(state) {
            return state.meta
        },

        currentEstimacion(state) {
            return state.currentEstimacion;
        }
    }
}
