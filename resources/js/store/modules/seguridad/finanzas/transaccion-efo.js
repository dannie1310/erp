const URI = '/api/SEGURIDAD_ERP/transaccion-efo/';


export default {
    namespaced: true,
    state: {
        transacciones: [],
        currenttransaccion: null,
        meta: {}
    },

    mutations: {
        SET_TRANSACCIONES(state, data) {
            state.transacciones = data
        },

        SET_TRANSACCION(state, data) {
            state.currenttransaccion = data;
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
                        reject(error)
                    })
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
                        reject(error)
                    })
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
                    })
            })
        },
    },

    getters: {
        transacciones(state) {
            return state.transacciones
        },
        meta(state) {
            return state.meta
        }
    }
}
