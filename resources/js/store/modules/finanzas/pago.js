const URI = '/api/finanzas/pago/';

export default {
    namespaced: true,
    state: {
        pagos: [],
        currentPago: null,
        meta: {}
    },

    mutations: {
        SET_PAGOS(state, data) {
            state.pagos = data;
        },

        SET_PAGO(state, data) {
            state.currentPago = data;
        },
        UPDATE_FONDO(state, data){
            state.pagos= state.pagos.map(pago => {
                if(pago.id === data.id){
                    return Object.assign({}, pago, data)
                }
                return pago
            })
            state.currentPago = data ;
        },

        SET_META(state, data) {
            state.meta = data;
        },
        UPDATE_ATTRIBUTE(state, data) {
            _.set(state.currentPago, data.attribute, data.value);
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



    },

    getters: {
        pagos(state) {
            return state.pagos;
        },

        meta(state) {
            return state.meta;
        },

        currentPago(state) {
            return state.currentPago;
        }
    }
}
