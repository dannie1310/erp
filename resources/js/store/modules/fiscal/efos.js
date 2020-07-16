const URI = '/api/fiscal/efos/';
export default {
    namespaced: true,
    state: {
        efos: [],
        currentEfo: null,
        meta: {},
    },

    mutations: {
        SET_EFOS(state, data) {
            state.efos = data;
        },
        SET_META(state, data) {
            state.meta = data;
        },
        SET_EFO(state, data) {
            state.currentEfo = data;
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
        efos(state) {
            return state.efos
        },
        meta(state) {
            return state.meta
        },
        currentEfo(state) {
            return state.currentEfo
        }
    }
}
