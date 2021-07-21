const URI = '/api/moneda/';

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
        monedasGlobales(context, payload = {}) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI+'monedasGlobales', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        monedasBase(context, payload = {}) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI+'monedasBase', payload)
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        }
    },

    getters: {
        monedas(state) {
            return state.monedas
        }
    }
}
