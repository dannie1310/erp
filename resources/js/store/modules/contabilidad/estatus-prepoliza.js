const URI = '/api/contabilidad/estatus-prepoliza';

export default {
    namespaced: true,
    state: {
        estatus: []
    },

    mutations: {
        SET_ESTATUS(state, data) {
            state.estatus = data
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
        estatus(state) {
            return state.estatus
        }
    }
}