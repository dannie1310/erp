export default {
    namespaced: true,

    state: {
        obras: [],
        meta: {},
        total:0,
        currentPage:0
    },

    mutations: {
        SET_OBRAS(state, obras) {
            state.obras = obras;
        },
        SET_META(state, data) {
            state.meta = data;
        },
        SET_PAGES(state, data){
            state.total = data.pagination.total_pages;
            state.currentPage = data.pagination.current_page;
        },


    },

    actions: {
        fetch (context, payload = { }){
            axios.get('/api/auth/obras/paginate', { params: payload.params})
                .then(r => r.data)
                .then(data => {
                    context.commit('SET_OBRAS', data.data)
                    context.commit('SET_META', data.meta)
                    context.commit('SET_PAGES', data.meta)
                })
        },

    },

    getters: {
        obrasAgrupadas(state) {
            return _.groupBy(state.obras, 'base_datos');
        },
        metas(state) {
            return state.meta;
        },
        totalPages(state){
            return state.total;
        },
        currentPage(state){
            return state.currentPage;
        }
    }
}