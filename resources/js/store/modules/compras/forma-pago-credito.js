const URI = '/api/compras/forma-pago-credito';

export default {
    namespaced: true,
    state: {
        formasPago: [],
        currentFormaPago: null,
        meta: {}
    },

    mutations: {
        SET_FORMAS_PAGO(state, data) {
            state.formasPago = data
        },

        SET_FORMA_PAGO(state, data) {
            state.currentFormaPago = data;
        },

        SET_META(state, data) {
            state.meta = data
        },
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
                    .get(URI + 'paginate', { params: payload })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    });
            });
        }
    },

    getters: {
        formasPago(state) {
            return state.formasPago;
        },
        meta(state) {
            return state.meta;
        }
    }
}