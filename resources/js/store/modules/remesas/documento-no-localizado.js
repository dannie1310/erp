const URI = '/api/remesas/documento-no-localizado/';

export default {
    namespaced: true,
    state: {
        documentos: [],
        currentDocumento: null,
        meta:{}
    },

    mutations: {
        SET_DOCUMENTOS(state, data) {
            state.documentos = data
        },
        SET_DOCUMENTO(state, data) {
            state.currentDocumento = data;
        },
        SET_META(state, data) {
            state.meta = data;
        },
        UPDATE_ATTRIBUTE(state, data) {
            _.set(state.currentDocumento, data.attribute, data.value);
        },

        UPDATE_DOCUMENTO(state, data) {
            state.documentos = state.documentos.map(e => {
                if (e.id === data.id) {
                    return Object.assign({}, e, data)
                }
                return e
            })
            state.currentDocumento = data;
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
        rechazar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Rechazar Documento de Pago para proveedor no localizado ante el SAT",
                    text: "¿Está seguro de rechazar el pago?",
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
                                        title: "Pago rechazado correctamente",
                                        text: " ",
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
                            swal("Ingrese el motivo de rechazo del pago.",{icon: "error"});
                        }
                    });
            });
        },
        autorizar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Autorizar el pago del proveedor no localizado ante el SAT",
                    text: "¿Está seguro de que desea autorizar el pago?",
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
                                    swal("Pago autorizado correctamente", {
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
        documentos(state) {
            return state.documentos;
        },
        currentDocumento(state) {
            return state.currentDocumento;
        },
        meta(state) {
            return state.meta;
        },
    }
}
