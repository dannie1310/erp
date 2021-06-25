const URI = '/api/contabilidad-general/cuenta-saldo-negativo/';
export default {
    namespaced: true,
    state: {
        cuentas: [],
        currentCuenta: null,
        meta: {},
        mesSeleccionado:null,
        anioSeleccionado:null,
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
        SET_ANIO_SELECCIONADO(state, data) {
            state.anioSeleccionado = data;
        },
        SET_MES_SELECCIONADO(state, data) {
            state.mesSeleccionado = data;
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

        obtenerInforme(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + payload.id + '/obtener-informe', payload)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },

        obtenerInformeMovimientos(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + payload.id + '/obtener-informe-movimientos', payload)
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
        },

        anioSeleccionado(state){
            return state.anioSeleccionado
        },

        mesSeleccionado(state){
            return state.mesSeleccionado
        }
    }
}
