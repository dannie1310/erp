const URI = '/api/control-recursos/factura/';

export default {
    namespaced: true,
    state: {
        solicitudes: [],
        currentSolicitud: null,
        meta: {}
    },

    mutations: {
        SET_SOLICITUDES(state, data) {
            state.solicitudes = data
        },
        SET_SOLICITUD(state, data)
        {
            state.currentSolicitud = data;
        },
        SET_META(state, data) {
            state.meta = data;
        },
        UPDATE_SOLICITUD(state, data){
            state.solicitudes = state.solicitudes.map(solicitud => {
                if(solicitud.id === data.id){
                    return Object.assign({}, solicitud, data)
                }
                return solicitud
            })
            state.currentSolicitud = data ;
        },
        UPDATE_ATTRIBUTE(state, data) {
            state.currentSolicitud[data.attribute] = data.value
        },
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
        cargarXML(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'xml', payload.data, payload.config)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        storeCFDI(context, payload){
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar la Factura",
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
                            .post(URI+ 'CFDI', payload)
                            .then(r => r.data)
                            .then(data => {
                                swal("Factura registrada correctamente", {
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
