const URI = '/api/contabilidad-general/solicitud-edicion-poliza/';
export default {
    namespaced: true,
    state: {
        solicitudes: [],
        currentSolicitud: null,
        meta: {},
    },

    mutations: {
        SET_SOLICITUDES(state, data) {
            state.solicitudes = data;
        },

        SET_META(state, data) {
            state.meta = data;
        },

        UPDATE_SOLICITUD(state, data) {
            state.solicitudes = state.solicitudes.map(poliza => {
                if (solicitud.id === data.id) {
                    return Object.assign({}, solicitud, data)
                }
                return solicitud
            })
            state.currentSolicitud = state.currentSolicitud ? data : null;
        },

        SET_SOLICITUD(state, data) {
            state.currentSolicitud = data;
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

        cargarLayout(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'carga-masiva', payload.data, payload.config)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        store(context, payload){
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Solicitud",
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
                }).then((value) => {
                    if (value) {
                        axios
                            .post(URI, payload)
                            .then(r => r.data)
                            .then(data => {
                                swal("Solicitud registrada correctamente", {
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
        autorizar(context, payload){
            return new Promise((resolve, reject) => {
                swal({
                    title: "Autorización de solicitud de edición de póliza",
                    text: "¿Está seguro de que desea autorizar esta solicitud?",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Autorizar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI + payload.id + '/autorizar', payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Solicitud autorizada correctamente", {
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
        rechazar(context, payload){
            return new Promise((resolve, reject) => {
                swal({
                    title: "Rechazo de solicitud de edición de póliza",
                    text: "¿Está seguro de que desea rechazar esta solicitud?",
                    icon: "warning",
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
                                .post(URI + payload.id + '/rechazar', payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Solicitud rechazada correctamente", {
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
        aplicar(context, payload){
            return new Promise((resolve, reject) => {
                swal({
                    title: "Aplicación de solicitud de edición de póliza",
                    text: "¿Está seguro de que desea aplicar esta solicitud de edición?",
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
                                .post(URI + payload.id + '/aplicar', payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Solicitud aplicada correctamente", {
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
        descargaXLS(context, payload){
            /*axios
                .get(URI + payload.id + '/descargar-xls', payload)
                .then(r => r.data)
                .then(data => {
                    swal("Solicitud descargada correctamente", {
                        icon: "success",
                        timer: 1500,
                        buttons: false
                    })
                })*/

            var urr = URI + payload.id + '/descargar-xls'+ '?access_token=' + this._vm.$session.get('jwt');
            var win = window.open(urr, "_blank");

            win.onbeforeunload = () => {
                swal("Solicitud descargada correctamente.", {
                    icon: "success",
                    timer: 2000,
                    buttons: false
                })
            }
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
            return state.currentSolicitud
        }
    }
}