const URI = '/api/control-recursos/solicitud-cheque/';

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
        descargar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Descargar Layout",
                    text: "¿Está seguro de que la información es correcta?",
                    icon: "info",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Descargar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI+'layout', payload)
                                .then(r => r.data)
                                .then(data => {
                                    const url = window.URL.createObjectURL(new Blob([response.data]));
                                    const link = document.createElement("a");
                                    link.href = url;
                                    link.setAttribute(data, "file.zip"); //or any other extension
                                    document.body.appendChild(link);
                                    link.click();
                                })
                                .catch(error => {
                                    reject(error);
                                });
                        }
                    });
            });
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
