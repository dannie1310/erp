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
                axios
                    .post(URI + 'layout', { params: payload })
                    .then(r => r.data)
                    .then(data => {
                        if(data === true)
                        {
                            swal("Layout de bancario control de recursos descargado previemente.", {
                                icon: "warning",
                                timer: 3000,
                                buttons: false
                            })
                        }else {
                            var URL = '/api/control-recursos/solicitud-cheque/' + data + '/descarga?&access_token=' + this._vm.$session.get('jwt');
                            var win = window.open(URL, "_blank");
                            win.onbeforeunload = () => {
                                axios
                                    .get(URI + data, {params: payload.params})
                                    .then(r => r.data)
                                    .then(dat => {
                                        swal("Layout bancario control de recursos descargado correctamente.", {
                                            icon: "success",
                                            timer: 2000,
                                            buttons: false
                                        })
                                        resolve(dat);
                                    })
                                    .catch(error => {
                                        reject(error);
                                    })
                            }
                        }
                    })
                    .catch(error => {
                        reject(error);
                    })
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
