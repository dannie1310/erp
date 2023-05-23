const URI = '/api/activo-fijo/lista-departamento/';

export default {
    namespaced: true,
    state: {
        departamentos: []
    },

    mutations: {
        SET_DEPARTAMENTOS(state, data) {
            state.departamentos = data;
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
    },

    getters: {
        departamentos(state) {
            return state.departamentos;
        },
        meta(state) {
            return state.meta;
        },
    }
}
