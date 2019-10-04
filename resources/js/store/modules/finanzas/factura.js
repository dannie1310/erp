const URI = '/api/finanzas/factura/';

export default {
    namespaced: true,
    state: {
        facturas: [],
        currentFactura: null,
        meta: {}
    },

    mutations: {
        SET_FACTURAS(state, data) {
            state.pagos = data;
        },

        SET_FACTURA(state, data) {
            state.currentPago = data;
        },

        SET_META(state, data) {
            state.meta = data;
        },
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
                    });
            });
        },
        autorizadas(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'autorizada', { params: payload.params })
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
        facturas(state) {
            return state.facturas;
        },

        meta(state) {
            return state.meta;
        },

        currentFactura(state) {
            return state.currentFactura;
        }
    }
}
