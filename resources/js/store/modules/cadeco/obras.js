export default {
    namespaced: true,

    state: {
        obras: []
    },

    mutations: {
        fetch(state, obras) {
            state.obras = obras;
        }
    },

    actions: {
        fetch (context, payload = { }){
            axios.get('/api/auth/obras', { params: payload.params})
                .then(res => {
                    context.commit('fetch', res.data)
                })
        }
    },

    getters: {
        obrasAgrupadas(state) {
            return _.groupBy(state.obras, 'base_datos');
        }
    }
}