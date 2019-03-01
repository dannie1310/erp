const URI = '/api/contabilidad/tipo-cuenta-contable/';

export default {
    namespaced: true,
    state: {
        tipos: [],
        currentTipo: null,
        meta: {}
    },

    mutations: {
        SET_TIPOS(state, data) {
            state.tipos = data
        },

        SET_META(state, data) {
            state.meta = data
        },

        SET_TIPO(state, data) {
            state.currentTipo = data
        },
    },

    actions: {
        index(context, payload) {
            axios.get(URI, {params: payload})
                .then(r => r.data)
                .then((data) => {
                    context.commit('SET_TIPOS', data.data)
                })
        },
        paginate (context, payload){
            context.commit('SET_TIPOS', [])
            axios
                .get(URI + 'paginate', { params: payload })
                .then(r => r.data)
                .then(data => {
                    context.commit('SET_TIPOS', data.data)
                    context.commit('SET_META', data.meta)
                })
        },
    },

    getters: {
        tipos(state) {
            return state.tipos
        },

        meta(state) {
            return state.meta
        },

        currentTipo(state) {
            return state.currentTipo
        }
    }
}