const URI = '/api/contabilidad/tipo-movimiento/';

export default {
    namespaced: true,
    state: {
        tipos: []
    },

    mutations: {
        fetch(state, payload) {
            state.tipos = payload.data
        }
    },

    actions: {
        find(context, payload) {
            return new Promise((resolve, reject) => {
                axios.get(URI + payload.id, {params: payload.params})
                    .then(res => {
                        resolve(res.data)
                    })
                    .catch(err => {
                        reject(err)
                    })
            });

        }
    },

    getters: {
        tipos (state) {
            return state.tipos
        }
    }
}