const URI = '/api/contabilidad/tipo-poliza-contpaq';

export default {
    namespaced: true,
    state: {
        tipos: []
    },

    mutations: {
        fetch(state, payload) {
            state.tipos = payload.data
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
        tipos(state) {
            return state.tipos
        }
    }
}