const URI = '/api/control-recursos/entrega/';

export default {
    namespaced: true,
    state: {
        entregas: []
    },

    mutations: {
        SET_ENTREGAS(state, data) {
            state.etregas = data
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
        entregas(state) {
            return state.entregas
        }
    }
}
