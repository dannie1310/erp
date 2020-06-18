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
            state.empresas = data;
        },

        SET_EMPRESA(state, data) {
            state.currentEmpresa = data;
        },

        SET_CUENTA_EMPRESA(state, data) {
            state.empresas.forEach(e => {
                if(e.id == data.empresa.id) {
                    e.cuentasEmpresa.data.push(data);
                }
            });
        },

        SET_META(state, data) {
            state.meta = data;
        }
    },

    actions: {
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
                    });
            });
        },
        store(context,payload){
            return new Promise((resolve, reject) => {
                axios
                    .post('/api/empresa/', payload)
                    .then(r => r.data)
                    .then(data => {
                        resolve(data.id);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },
        detalleUnificacion(context,payload){
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id +  '/detalleUnificacion', { params: payload.params })
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
            return state.empresas;
        },

        meta(state) {
            return state.meta;
        },

        currentEmpresa(state) {
            return state.currentEmpresa;
        }
    }
}
