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
        aplicar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Aplicar Solicitud de Cambio",
                    icon: "warning",
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
                            .patch(URI + payload.id + '/aplicar')
                            .then(r => r.data)
                            .then(data => {
                                swal("Solicitud de cambio aplicada correctamente", {
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
        rechazar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Rechazar Solicitud de Cambio",
                    text: "¿Está seguro de que desea rechazar esta solicitud de cambio?",
                    icon: "warning",
                    closeOnClickOutside: false,
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Rechazar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .patch(URI + payload.id + '/rechazar',payload.params)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Solicitud de cambio rechazada correctamente", {
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
        cancelar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Cancelar Solicitud de Cambio",
                    text: "¿Está seguro de que desea cancelar esta solicitud de cambio?",
                    icon: "warning",
                    closeOnClickOutside: false,
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Cancelar Solicitud',
                            closeModal: false,
                        }
                    }
                })
                .then((value) => {
                    if (value) {
                        axios
                            .patch(URI + payload.id + '/cancelar',{params: payload.params})
                            .then(r => r.data)
                            .then(data => {
                                swal("Solicitud de cambio cancelada correctamente", {
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
        procesarLayoutExtraordinarios(context, payload){
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'procesar-layout-extraordinarios', payload.data, payload.config)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        procesarLayoutCambioPrecioVolumen(context, payload){
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'procesar-layout-cambio-precio-volumen', payload.data, payload.config)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        }
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
