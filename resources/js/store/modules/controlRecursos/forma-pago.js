const URI = '/api/control-recursos/forma-pago/';

export default {
    namespaced: true,
    state: {
        formas: []
    },

    mutations: {
        SET_FORMAS(state, data) {
            state.formas = data
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
        formas(state) {
            return state.formas
        }
    }
}
