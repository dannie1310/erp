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
