const URI = '/api/finanzas/gestion-pago/';

export default {
    namespaced: true,
    state: {
        bitacora: [],
        meta: {}
    },
    mutations: {
        SET_BITACORA(state, data) {
            state.bitacora = data
        }
    },
    actions: {
        cargarBitacora(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'bitacora', payload.data, payload.config)
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
        bitacora(state) {
            return state.bitacora;
        }
    },
}
