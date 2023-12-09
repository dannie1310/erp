const URI = '/api/control-recursos/pago-a-proveedor/';

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
                        reject(error)
                    })
            });
        },
        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Solicitud Pago A Proveedor",
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
                                    swal("Solicitud registrada correctamente.", {
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
