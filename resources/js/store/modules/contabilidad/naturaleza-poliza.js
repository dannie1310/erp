const URI = '/api/contabilidad/naturaleza-poliza/';

export default {
    namespaced: true,
    state: {
        naturalezas: []
    },

    mutations: {
        SET_NATURALEZAS(state, data) {
            state.naturalezas = data
        }
    },

    actions: {
        index(context, payload) {
            axios.get(URI, {params: payload})
                .then(r => r.data)
                .then((data) => {
                    context.commit('SET_NATURALEZAS', data.data)
                })
        }
    },

    getters: {
        naturalezas(state) {
            return state.naturalezas
        }
    }
}