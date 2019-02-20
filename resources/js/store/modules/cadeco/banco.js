const URI = '/api/banco/';

export default {
    namespaced: true,
    state: {
        cuentas: [],
        currentBanco: null,
        meta: {}
    },

    mutations: {
        SET_BANCOS(state, data) {
            state.cuentas = data
        },

        SET_BANCO(state, data) {
            state.currentBanco = data
        },

        SET_META(state, data) {
            state.meta = data
        }
    },

    actions: {
        find(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id, { params: payload.params })
                    .then(r => r.data)
                    .then((data) => {
                        context.commit('SET_BANCO', data)
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },

        index(context, payload) {
            return new Promise((resolve, reject) => {
                context.commit('SET_BANCOS', null)
                axios
                    .get(URI, { params: payload.params })
                    .then(r => r.data)
                    .then((data) => {
                        context.commit('SET_BANCOS', data.data)
                        resolve();
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },

        paginate(context, payload) {
            return new Promise((resolve, reject) => {
                context.commit('SET_BANCOS', []);
                axios
                    .get(URI + 'paginate', { params: payload })
                    .then(r => r.data)
                    .then(data => {
                        context.commit('SET_BANCOS', data.data);
                        context.commit('SET_META', data.meta);
                        resolve();
                    })
                    .catch(error => {
                        reject(error);
                    });
            });
        }
    },

    getters: {
        bancos(state) {
            return state.cuentas
        },

        meta(state) {
            return state.meta
        },

        currentBanco(state) {
            return state.currentBanco
        }
    }
}