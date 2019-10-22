const URI = '/api/inventario/';

export default {
    namespaced: true,
    state: {
        inventarios: []
    },

    mutations: {
        SET_INVENTARIOS(state, data) {
            state.inventarios = data;
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
                        reject(error);
                    });
            });
        }
    },

    getters: {
        inventarios(state) {
            return state.inventarios
        }
    }
}
