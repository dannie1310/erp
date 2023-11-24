const URI = '/api/control-recursos/firma-firmante/';

export default {
    namespaced: true,
    state: {
        firmas: []
    },

    mutations: {
        SET_FIRMAS(state, data) {
            state.firmas = data
        }
    },

    actions: {
        index(context, payload = {}) {
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
        firmas(state) {
            return state.firmas
        }
    }
}
