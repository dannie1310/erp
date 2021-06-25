const URI = '/api/entrega-cfdi/ctg-tipo-archivo/';

export default {
    namespaced: true,
    state: {
        tipos: [],
        currentTipo: null,
        meta: {}
    },

    mutations: {
        SET_TIPOS(state, data) {
            state.tipos = data;
        },

        SET_TIPO(state, data) {
            state.currentTipo = data;
        },

        SET_META(state, data) {
            state.meta = data;
        }
    },

    actions: {
        index(context, payload) {
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
        },
    },
    getters: {
        tipos(state) {
            return state.tipos;
        },

        meta(state) {
            return state.meta;
        },

        currentTipo(state) {
            return state.currentTipo;
        }
    }
}
