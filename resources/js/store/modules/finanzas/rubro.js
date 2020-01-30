const URI = '/api/finanzas/rubro/';

export default {
    namespaced: true,
    state: {
        rubros: []
    },

    mutations: {
        SET_RUBROS(state, data) {
            state.rubros = data
        }
    },

    actions: {
        index(context, payload = {}) {
            return new Promise((resolve, reject) => {
                axios.get(URI, { params: payload.params })
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
        rubros(state) {
            return state.rubros
        }
    }
}