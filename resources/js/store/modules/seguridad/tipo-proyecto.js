const URI = '/api/SEGURIDAD_ERP/tipo-proyecto/';

export default {
    namespaced: true,

    state: {
        tipos: []
    },

    mutations: {
        SET_TIPOS(state, data) {
            state.tipos = data;
        }
    },

    actions: {
        index(context, payload = {}) {
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
        tipos(state) {
            return state.tipos;
        }
    }
}