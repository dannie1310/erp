const URI = '/api/fiscal/proveedor-rep/';
export default {
    namespaced: true,
    state: {
        proveedores_rep: [],
        currentProveedorREP: null,
        meta: {},
    },

    mutations: {
        SET_PROVEEDORES_REP(state, data) {
            state.proveedores_rep = data;
        },

        SET_META(state, data) {
            state.meta = data;
        },

        SET_PROVEEDOR_REP(state, data) {
            state.currentProveedorREP = data;
        }
    },

    actions: {
        index(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI, { params: payload.params })
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },

        paginate (context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'paginate', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        context.commit("SET_PROVEEDORES_REP", data.data);
                        context.commit("SET_META", data.meta);
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            })
        },
    },

    getters: {
        proveedores_rep(state) {
            return state.proveedores_rep
        },

        meta(state) {
            return state.meta
        },

        currentProveedorREP(state) {
            return state.currentProveedorREP
        }
    }
}
