const URI = '/api/acarreos/empresa/';

export default{
    namespaced: true,
    state: {
        empresas: [],
        currentEmpresa: null,
        meta: {}
    },

    mutations: {
        SET_EMPRESAS(state, data){
            state.empresas = data
        },

        SET_META(state, data){
            state.meta = data
        },

        SET_EMPRESA(state, data){
            state.currentEmpresa = data
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
        paginate(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'paginate', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            })
        },
        find(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id, { params: payload.params })
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
