const URI = '/api/contratos/solicitud-cambio/';

export default {
    namespaced: true,
    state: {
        solicitudes: [],
        currentSolicitud: null,
        meta: {},
    },

    mutations: {
        SET_SOLICITUDES(state, data) {
            state.solicitudes = data
        },

        SET_SOLICITUD(state, data) {
          state.currentSolicitud = data;
        },

        SET_META(state, data) {
            state.meta = data
        },

        APROBAR_SOLICITUD(state, id) {
            state.solicitudes.forEach(solicitud => {
                if(solicitud.id == id) {
                    solicitud.estado = 1;
                }
            })
        },

        REVERTIR_APROBACION(state, id) {
            state.solicitudes.forEach(solicitud => {
                if(solicitud.id == id) {
                    solicitud.estado = 0;
                }
            })
        },
        DELETE_SOLICITUD(state, id) {
            state.solicitudes = state.solicitudes.filter(solicitud => {
                return solicitud.id != id
            });
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
                    title: "Registrar Solicitud de Cambio",
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
                                    swal("Solicitud de cambio registrada correctamente", {
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
                    title: "¿Está seguro?",
                    text: "Aprobar Solicitud de Cambio",
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
                                swal("Solicitud de cambio aprobada correctamente", {
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
                    title: "¿Está seguro?",
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
        },
        eliminar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Eliminar Solicitud de Cambio",
                    text: "¿Está seguro de que desea eliminar esta solicitud de cambio?",
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
                                swal("Solicitud de cambio eliminada correctamente", {
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
        solicitudes(state) {
            return state.solicitudes
        },

        meta(state) {
            return state.meta
        },

        currentSolicitud(state) {
            return state.currentSolicitud;
        }
    }
}
