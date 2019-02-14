const URI = '/api/cuenta/';

export default {
    namespaced: true,
    state: {
        cuentas: [],
        currentCuenta: null
    },

    mutations: {
        SET_CUENTAS(state, data) {
            state.cuentas = data
        }
    },

    actions: {
        fetch(context, payload) {
            axios
                .get(URI, { params: payload })
                .then(r => r.data)
                .then((data) => {
                    context.commit('SET_CUENTAS', data.data)
                })
        }
    },

    getters: {
        cuentas(state) {
            return state.cuentas
        }
    }
}