const URI = '/api/contabilidad-general/contabilidad-electronica/';

export default {
    namespaced: true,
    state: {
        cuentas: [],
        currentCuenta: null,
        meta: {}
    },

    mutations: {
        SET_CUENTAS(state, data) {
            state.cuentas = data;
        },
        SET_CUENTA(state, data) {
            state.currentCuenta = data;
        },
        SET_META(state, data) {
            state.meta = data;
        },
    },
    actions: {
        cargarXML(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'xml', payload.data, payload.config)
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
            return state.cuentas;
        },
        meta(state) {
            return state.meta;
        },
        currentCuenta(state) {
            return state.currentCuenta;
        },
    }
}
