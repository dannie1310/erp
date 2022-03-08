const URI = '/api/finanzas-general/solicitud-pago/';

export default {
    namespaced: true,
    state: {
        solicitudes: [],
        currentSolicitud: null,
        meta:{}
    },

    mutations: {
        SET_SOLICITUDES(state, data) {
            state.solicitudes = data
        },
        SET_SOLICITUD(state, data) {
            state.currentSolicitud = data;
        },
        SET_META(state, data) {
            state.meta = data;
        },
        UPDATE_ATTRIBUTE(state, data) {
            _.set(state.currentSolicitud, data.attribute, data.value);
        },

        UPDATE_SOLICITUD(state, data) {
            state.solicitudes = state.solicitudes.map(e => {
                if (e.id === data.id) {
                    return Object.assign({}, e, data)
                }
                return e
            })
            state.currentSolicitud = data;
        },
    },

    actions: {
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
                    })
            })
        },
        index(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        rechazar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Rechazar solicitud de pago",
                    text: "¿Está seguro de rechazar la solicitud de pago?",
                    dangerMode: true,
                    icon: "info",
                    content: {
                        element: "input",
                        attributes: {
                            placeholder: "Motivo de Rechazo",
                            type: "text",
                        },
                    },
                    buttons: [
                        'Cancelar',
                        {
                            text: "Si, Rechazar",
                            closeModal: false,
                        }
                    ]
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .patch(URI+ payload.id+'/rechazar',  {id:payload.id, motivo: value}, { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal({
                                        title: "",
                                        text: "Transacción rechazada correctamente",
                                        icon: "success",
                                        timer: 3000,
                                        buttons: false
                                    }).then(() => {
                                        resolve(data);

                                    })
                                })
                                .catch(error => {
                                    reject(error);
                                });
                        } else {
                            swal("Ingrese el motivo de rechazo de la transacción.",{icon: "error"});
                        }
                    });
            });
        },
        autorizar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Autorizar solicitud de pago",
                    text: "¿Está seguro de que desea autorizar que la solicitud de pago se incluya en la remesa del proyecto?",
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
                    },
                    dangerMode: true,
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .get(URI + payload.id+'/autorizar', { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Transacción autorizada correctamente", {
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
    },

    getters: {
        solicitudes(state) {
            return state.solicitudes;
        },
        currentSolicitud(state) {
            return state.currentSolicitud;
        },
        meta(state) {
            return state.meta;
        },
    }
}
