const URI = '/api/control-recursos/proveedor/';

export default {
    namespaced: true,
    state: {
        proveedores: [],
        currentProveedor: null,
        meta: {}
    },

    mutations: {
        SET_PROVEEDORES(state, data) {
            state.proveedores = data;
        },

        SET_PROVEEDOR(state, data) {
            state.currentProveedor = data;
        },

        SET_META(state, data) {
            state.meta = data;
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
                    })
            });
        },
    },

    getters: {
        proveedores(state) {
            return state.proveedores;
        },

        meta(state) {
            return state.meta;
        },

        currentProveedor(state) {
            return state.currentProveedor;
        }
    }
}
