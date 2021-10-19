const URI = '/api/contabilidad-general/proveedor-sat/';
export default {
    namespaced: true,
    state: {
        proveedoresSat: [],
        currentProveedoresSat: null,
        meta: {},
    },

    mutations: {
        SET_PROVEEDORES_SAT(state, data) {
            state.proveedoresSat = data;
        },

        SET_META(state, data) {
            state.meta = data;
        },

        SET_PROVEEDOR_SAT(state, data) {
            state.currentProveedoresSat = data;
        },
    },

    actions: {
        index(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI, {params: payload.params})
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    });
            });
        },
        paginate (context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'paginate', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            })
        },
        buscarProveedoresSat(context, payload){
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'buscarProveedoresSat', {params: payload.params})
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
        proveedoresSat(state) {
            return state.proveedoresSat
        },

        meta(state) {
            return state.meta
        },

        currentProveedoresSat(state) {
            return state.currentProveedoresSat
        }
    }
}
