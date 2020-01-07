const URI = '/api/proveedor-contatista/';

export default {
    namespaced: true,
    state: {
        proveedor_contratistas: [],
        currentProveeedor: null,
        meta: {}
    },

    mutations: {
        SET_PROVEEDOR_CONTRATISTAS(state, data) {
            state.proveedor_contratistas = data;
        },

        SET_PROVEEDOR_CONTRATISTA(state, data) {
            state.currentProveeedor = data;
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


    },

    getters: {
        proveedorContratistas(state) {
            return state.proveedor_contratistas;
        },

        meta(state) {
            return state.meta;
        },

        currentProveeedor(state) {
            return state.currentProveeedor;
        }
    }
}
