const URI = '/api/IGH/menu/'
export default {
    namespaced: true,

    state: {
        aplicaciones: []
    },

    mutations: {
        SET_APLICACIONES(state, data) {
            state.aplicaciones = data;
        }
    },

    actions: {
        index(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI, { params: payload.params })
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data.data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        }
    },

    getters: {
        aplicaciones(state) {
            return state.aplicaciones;
        }
    }
}