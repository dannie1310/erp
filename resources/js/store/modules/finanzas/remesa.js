const URI = '/api/finanzas/remesa/';

export default {
    namespaced: true,
    state: {
        remesas: [],
        currentRemesa: null,
    },

    mutations: {
        SET_REMESAS(state, data) {
            state.remesas = data
        },
        SET_REMESA(state, data) {
            state.currentRemesa = data;
        }
    },

    actions: {
        index(context, payload = {}) {
            return new Promise((resolve, reject) => {

                axios
                    .get(URI, payload.config)
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
        remesas(state) {
            return state.remesas
        },
        currentRemesa(state) {
            return state.currentRemesa
        }
    }
}