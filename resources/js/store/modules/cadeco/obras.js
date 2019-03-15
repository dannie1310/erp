export default {
    namespaced: true,

    state: {
        obras: [],
        meta: {}
    },

    mutations: {
        SET_OBRAS(state, obras) {
            state.obras = obras;
        },
        SET_META(state, data) {
            state.meta = data;
        }
    },

    actions: {
        paginate (context, payload = { }){
            axios.get('/api/auth/obras/paginate', { params: payload.params})
                .then(r => r.data)
                .then(data => {
                    context.commit('SET_OBRAS', data.data)
                    context.commit('SET_META', data.meta)
                })
        },

    },

    getters: {
        obrasAgrupadas(state) {
            return _.groupBy(state.obras, 'base_datos');
        },
        meta(state) {
            return state.meta;
        }
    }
}