const URI = '/api/formato/orden-pago-estimacion';

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
        index(context, payload) {
            axios.get(URI, {params: payload})
                .then(r => r.data)
                .then((data) => {
                    context.commit('SET_ESTIMACIONES', data.data)
                })
        }
    },

    getters: {
        estimaciones(state) {
            return state.estimaciones
        }
    }
}