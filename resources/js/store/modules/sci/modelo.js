const URI = '/api/SCI/modelo/';

export default {
    namespaced: true,
    state: {
        modelos: []
    },

    mutations: {
        SET_MODELOS(state, data) {
            state.modelos = data;
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
        modelos(state) {
            return state.modelos;
        },
        meta(state) {
            return state.meta;
        },

    }
}
