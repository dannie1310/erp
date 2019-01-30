const URI = '/api/contabilidad/cuenta-general/';

export default {
    namespaced: true,
    state: {
        cuentas: [],
        currentCuenta: null,
        meta: {}
    },

    mutations: {
        SET_CUENTAS(state, data) {
            state.cuentas = data
        },

        SET_META(state, data) {
            state.meta = data
        },

        SET_CUENTA(state, data) {
            state.currentCuenta = data
        },

        UPDATE_CUENTA(state, data) {
            state.cuentas = state.cuentas.map(cuenta => {
                if (cuenta.id === data.id) {
                    return Object.assign([], cuenta, data)
                }
                return cuenta
            })
            state.currentCuenta = data
        },

        UPDATE_ATTRIBUTE(state, data) {
            state.currentCuenta[data.attribute] = data.value
        }
    },

    actions: {
        paginate (context, payload){
            axios.get(URI + 'paginate', {params: payload})
                .then(r => r.data)
                .then(data => {
                    context.commit('SET_CUENTAS', data.data)
                    context.commit('SET_META', data.meta)
                })
        },

        find(context, id) {
            context.commit('SET_CUENTA', null)
            axios.get(URI + id)
                .then(r => r.data)
                .then(data => {
                    context.commit('SET_CUENTA', data)
                })
        },

        update(context, payload) {
            axios.patch(URI + payload.id, payload)
                .then(r => r.data)
                .then(data => {
                    context.commit('UPDATE_CUENTA', data);
                })
        }
    },

    getters: {
        cuentas(state) {
            return state.cuentas
        },

        meta(state) {
            return state.meta
        },

        currentCuenta(state) {
            return state.currentCuenta
        }
    }
}