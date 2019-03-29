const URI = '/api/contratos/estimacion/';

export default {
    namespaced: true,
    state: {
        estimaciones: []
    },

    mutations: {
        SET_ESTIMACIONES(state, data) {
            state.estimaciones = data
        }
    },

    actions: {
        pdf(context, payload) {
            axios.get(URI + payload+'/formato-orden-pago', {params: payload.params})
                .then((data) => {

                });
        },
    },
    getters: {
        estimaciones(state) {
            return state.estimaciones
        }
    }
}