const URI = '/api/nominas/empresa/';
export default {
    namespaced: true,
    state: {
        empresas: [],
        currentEmpresa: null,
        meta: {},
    },

    mutations: {
        SET_EMPRESAS(state, data) {
            state.empresas = data;
        },

        SET_META(state, data) {
            state.meta = data;
        },

        UPDATE_EMPRESA(state, data) {
            state.empresas = state.empresas.map(empresa => {
                if (empresa.id === data.id) {
                    return Object.assign({}, empresa, data)
                }
                return empresa
            })
            state.currentEmpresa = state.currentEmpresa ? data : null;
        },

        SET_EMPRESA(state, data) {
            state.currentEmpresa = data;
        }
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

        conectar(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'connect', payload.data, payload.config)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
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
