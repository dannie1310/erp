const URI = '/api/tesoreria/movimiento-bancario/';

export default {
    namespaced: true,
    state: {
        movimientos: [],
        currentMovimiento: null,
        meta: {},
        cargando: true
    },

    mutations: {
        SET_MOVIMIENTOS(state, data) {
            state.movimientos = data
        },

        SET_MOVIMIENTO(state, data) {
            state.currentMovimiento = data
        },

        SET_META(state, data) {
            state.meta = data
        },

        SET_CARGANDO(state, data) {
            state.cargando = data
        },
    },

    actions: {
        paginate (context, payload){
            axios.get(URI + 'paginate', {params: payload})
                .then(r => r.data)
                .then(data => {
                    context.commit('SET_MOVIMIENTOS', data.data)
                    context.commit('SET_META', data.meta)
                })
        },

        find(context, payload) {
            context.commit('SET_CARGANDO', true);
            axios.get(URI + payload.id, {params: payload.params})
                .then(r => r.data)
                .then((data) => {
                    context.commit('SET_MOVIMIENTO', data)
                    context.commit('SET_CARGANDO', false);
                })
        }
    },

    getters: {
        movimientos(state) {
            return state.movimientos
        },

        meta(state) {
            return state.meta
        },

        currentMovimiento(state) {
            return state.currentMovimiento
        }
    }
}