const URI = '/api/tesoreria/movimiento-bancario/';

export default {
    namespaced: true,
    state: {
        movimientos: [],
        meta: {}
    },

    mutations: {
        SET_MOVIMIENTOS(state, data) {
            state.movimientos = data
        },

        SET_META(state, data) {
            state.meta = data
        }
    },

    actions: {
        paginate (context, payload){
            axios.get(URI + 'paginate', {params: payload})
                .then(r => r.data)
                .then(data => {
                    context.commit('SET_MOVIMIENTOS', data.data)
                    context.commit('SET_META', data.meta)
                })
        }
    },

    getters: {
        movimientos(state) {
            return state.movimientos
        },

        meta(state) {
            return state.meta
        }
    }
}