const URI = '/api/activo-fijo/lista-usuario/';

export default {
    namespaced: true,
    state: {
        usuarios: []
    },

    mutations: {
        SET_USUARIOS(state, data) {
            state.usuarios = data;
        },
        SET_META(state, data) {
            state.meta = data;
        },
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
        },
        indexOrdenado(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI+"ordenado", { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    });
            });
        },
    },

    getters: {
        usuarios(state) {
            return state.usuarios;
        },
        meta(state) {
            return state.meta;
        },
    }
}
