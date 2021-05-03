const URI = '/api/fiscal/tipo-fecha-sat/';
export default {
    namespaced: true,
    state: {
        tipos: [],
        currentTipo: null,
        meta: {},
    },

    mutations: {
        SET_TIPOS(state, data) {
            state.tipos = data;
        },
        SET_META(state, data) {
            state.meta = data;
        },
        SET_TIPO(state, data) {
            state.currentTipo = data;
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
        tipos(state) {
            return state.tipos
        },
        meta(state) {
            return state.meta
        },
        currentTipo(state) {
            return state.currentTipo
        }
    }
}
