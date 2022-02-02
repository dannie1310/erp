const URI = '/api/finanzas-general/solicitud-pago/';

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

        SET_META(state, data) {
            state.meta = data
        },

        SET_SOLICITUD(state, data) {
            state.currentSolicitud = data
        },
        DELETE_SOLICITUD(state, data){
                state.currentSolicitud = data
        },
        UPDATE_SOLICITUD(state, data) {
            state.solicitudes = state.solicitudes.map(solicitud => {
                if (solicitud.id === data.id) {
                    return Object.assign({}, solicitud, data)
                }
                return solicitud
            })
            state.currentSolicitud = data;
        },
        UPDATE_ATTRIBUTE(state, data) {
            state.currentSolicitud[data.attribute] = data.value
        }
    },

    actions: {
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
                    })
            });
        },

        getIndicadorAplicadas(context, payload)
        {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI +'indicador-aplicadas', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },

        descargaExcel(context, payload)
        {
            var urr = URI + 'descarga-excel?access_token=' + this._vm.$session.get('jwt');
            var win = window.open(urr, "_blank");

            win.onbeforeunload = () => {
                swal("Información descargada correctamente.", {
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
