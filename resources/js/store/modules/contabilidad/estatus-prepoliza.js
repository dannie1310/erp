const URI = '/api/contabilidad/estatus-prepoliza';

export default {
    namespaced: true,
    state: {
        estatus: []
    },

    mutations: {
        SET_ESTATUS(state, data) {
            state.estatus = data
        }
    },

    actions: {
        fetch(context) {
            axios.get(URI)
                .then(r => r.data)
                .then(data => {
                    context.commit('SET_ESTATUS', data.data)
                })
        }
    },

    getters: {
        estatus(state) {
            return state.estatus
        }
    }
}