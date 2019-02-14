const URI = '/api/empresa/';

export default {
    namespaced: true,
    state: {
        empresas: [],
        currentEmpresa: null,
        meta: {}
    },

    mutations: {
        SET_EMPRESAS(state, data) {
            state.empresas = data
        },

        SET_EMPRESA(state, data) {
            state.currentEmpresa = data
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
                        context.commit('SET_EMPRESA', data)
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },

        index(context, payload) {
            return new Promise((resolve, reject) => {
                context.commit('SET_EMPRESAS', null)
                axios
                    .get(URI, { params: payload.params })
                    .then(r => r.data)
                    .then((data) => {
                        context.commit('SET_EMPRESAS', data.data)
                        resolve();
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },

        paginate(context, payload) {
            return new Promise((resolve, reject) => {
                context.commit('SET_EMPRESAS', []);
                axios
                    .get(URI + 'paginate', { params: payload })
                    .then(r => r.data)
                    .then(data => {
                        context.commit('SET_EMPRESAS', data.data);
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
        empresas(state) {
            return state.empresas
        },

        meta(state) {
            return state.meta
        },

        currentEmpresa(state) {
            return state.currentEmpresa
        }
    }
}