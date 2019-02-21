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
                context.commit('SET_FONDOS', null)
                axios
                    .get(URI, { params: payload.params })
                    .then(r => r.data)
                    .then((data) => {
                        context.commit('SET_FONDOS', data.data)
                        resolve(data.data);
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