const URI = '/api/control-recursos/caja-chica/';

export default {
    namespaced: true,
    state: {
        cajas: []
    },

    mutations: {
        SET_CAJAS(state, data) {
            state.cajas = data
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
        cajas(state) {
            return state.cajas
        }
    }
}
