const URI = '/api/seguimiento/cliente/';

export default {
    namespaced: true,
    state: {
        clientes: [],
        currentCliente: null,
        meta: {},
    },

    mutations: {
        SET_CLIENTES(state, data) {
            state.clientes = data;
        },

        SET_CLIENTE(state, data) {
            state.currentCliente = data;
        },

        SET_META(state, data) {
            state.meta = data;
        },
    },

    actions: {
        index(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
    },

    getters: {
        clientes(state) {
            return state.clientes;
        },
        meta(state) {
            return state.meta;
        },
        currentCliente(state) {
            return state.currentCliente;
        },
    }
}
