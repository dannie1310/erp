const URI = '/api/control-presupuesto/solicitud-cambio/';

export default {
    namespaced: true,
    state: {
        solicitudesCambio: [],
        currentSolicitudCambio: null,
        meta: {},
    },

    mutations: {
        SET_SOLICTUDES(state, data) {
            state.solicitudesCambio = data
        },
        SET_SOLICITUD(state, data) {
            state.currentSolicitudCambio = data;
        },
        SET_META(state, data) {
            state.meta = data;
        },
    },

    actions: {
        index(context, payload = {}) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI, {params : payload.params})
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data.data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
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
    },

    getters: {
        solicitudesCambio(state) {
            return state.solicitudesCambio
        },
        currentSolicitudCambio(state) {
            return state.currentSolicitudCambio
        },
        meta(state) {
            return state.meta
        },
    }
}