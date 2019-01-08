const URI = '/api/contabilidad/estatus-prepoliza';

export default {
    namespaced: true,
    state: {
        estatus: []
    },

    mutations: {
        fetch(state, payload) {
            state.estatus = payload.data
        }
    },

    actions: {
        fetch(context) {
            axios.get(URI)
                .then(res => {
                    context.commit('fetch', res.data)
                })
                .catch(err => {
                    alert(err);
                });
        }
    },

    getters: {
        estatus(state) {
            return state.estatus
        }
    }
}