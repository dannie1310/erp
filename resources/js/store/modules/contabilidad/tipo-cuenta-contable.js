const URI = '/api/contabilidad/tipo-cuenta-contable';

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
        fetch(context, payload) {
            axios.get(URI, {params: payload})
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