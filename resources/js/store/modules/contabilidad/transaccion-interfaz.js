const URI = '/api/contabilidad/transaccion-interfaz/';

export default {
    namespaced: true,
    state: {
        transacciones: []
    },

    mutations: {
        SET_TRANSACCIONES(state, data) {
            state.transacciones = data
        }
    },

    actions: {
        index (context, payload = {}) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI, payload.config)
                    .then(r => r.data)
                    .then(data => {
                        resolve(data)
                    })
                    .catch(error => {
                        reject(error)
                    })
            })
        }
    },

    getters: {
        transacciones(state) {
            return state.transacciones
        }
    }
}