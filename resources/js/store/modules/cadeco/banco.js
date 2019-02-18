const URI = '/api/banco/';

export default {
    namespaced: true,
    state: {
        bancos: [],
        currentBancos: null,
        meta: {}
    },

    mutations: {
        SET_BANCOS(state, data) {
            state.bancos = data
        },

        SET_BANCO(state, data) {
            state.currentBancos = data
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

        currentBancos(state) {
            return state.currentBancos
        }
    }
}