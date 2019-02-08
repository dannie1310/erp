const URI = '/api/almacen/';

export default {
    namespaced: true,
    state: {
        almacenes: []
    },

    mutations: {
        SET_ALMACENES(state, data) {
            state.almacenes = data
        }
    },

    actions: {
        index(context, payload) {
            return new Promise((resolve, reject) => {
                context.commit('SET_ALMACENES', null)
                axios
                    .get(URI, { params: payload.params })
                    .then(r => r.data)
                    .then((data) => {
                        context.commit('SET_ALMACENES', data.data)
                        resolve();
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        }
    },

    getters: {
        almacenes(state) {
            return state.almacenes
        }
    }
}