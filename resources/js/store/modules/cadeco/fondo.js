const URI = '/api/fondo/';

export default {
    namespaced: true,
    state: {
        fondos: [],
        currentFondo: null,
    },

    mutations: {
        SET_FONDOS(state, data) {
            state.fondos = data
        },

        SET_FONDO(state, data) {
            state.currentFondo = data;
        }
    },

    actions: {
        index(context, payload) {
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
        }
    },

    getters: {
        fondos(state) {
            return state.fondos
        }
    }
}