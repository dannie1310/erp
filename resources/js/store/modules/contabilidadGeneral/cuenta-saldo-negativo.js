const URI = '/api/contabilidad-general/cuenta-saldo-negativo/';
export default {
    namespaced: true,
    state: {
        cuentas: [],
        currentCuenta: null,
        meta: {},
    },

    mutations: {
        SET_CUENTAS(state, data) {
            state.cuentas = data;
        },

        SET_META(state, data) {
            state.meta = data;
        },

        SET_CUENTA(state, data) {
            state.currentCuenta = data;
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

        sincronizar(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'sincronizar', payload)
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
        cuentas(state) {
            return state.cuentas
        },

        meta(state) {
            return state.meta
        },

        currentCuenta(state) {
            return state.currentCuenta
        }
    }
}
