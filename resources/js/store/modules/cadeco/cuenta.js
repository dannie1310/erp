const URI = '/api/cuenta/';

export default {
    namespaced: true,
    state: {
        cuentas: [],
        currentCuenta: null,
        meta: {}
    },

    mutations: {
        SET_CUENTAS(state, data) {
            state.cuentas = data
        },
        SET_CUENTA(state, data) {
            state.currentCuenta = data
        },
        SET_META(state, data) {
            state.meta = data
        }
    },

    actions: {
        index(context, payload) {
            return new Promise((resolve, reject) => {
                context.commit('SET_CUENTAS', null)
                axios
                    .get(URI, { params: payload.params })
                    .then(r => r.data)
                    .then((data) => {
                        context.commit('SET_CUENTAS', data.data)
                        resolve();
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        paginate(context, payload) {
            return new Promise((resolve, reject) => {
                context.commit('SET_CUENTAS', []);
                axios
                    .get(URI + 'paginate', { params: payload })
                    .then(r => r.data)
                    .then(data => {
                        context.commit('SET_CUENTAS', data.data);
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
        cuentas(state) {
            return state.cuentas
        },
        meta(state) {
            return state.meta
        },
        currentCuenta(state) {
            return state.currentCuenta
        }
    }
}