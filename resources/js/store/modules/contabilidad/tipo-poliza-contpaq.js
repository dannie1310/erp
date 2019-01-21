const URI = '/api/contabilidad/tipo-poliza-contpaq';

export default {
    namespaced: true,
    state: {
        tipos: []
    },

    mutations: {
        SET_TIPOS(state, data) {
            state.tipos = data
        }
    },

    actions: {
        fetch(context) {
            axios.get(URI)
                .then(r => r.data)
                .then(data => {
                    context.commit('SET_TIPOS', data.data)
                })
        }
    },

    getters: {
        tipos(state) {
            return state.tipos
        }
    }
}