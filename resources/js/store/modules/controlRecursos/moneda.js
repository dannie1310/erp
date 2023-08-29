const URI = '/api/control-recursos/moneda/';

export default {
    namespaced: true,
    state: {
        monedas: []
    },

    mutations: {
        SET_MONEDAS(state, data) {
            state.monedas = data
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
        monedas(state) {
            return state.monedas
        }
    }
}
