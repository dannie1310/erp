const URI = '/api/seguimiento/proyecto/';

export default {
    namespaced: true,
    state: {
        proyectos: [],
        currentProyecto: null,
        meta: {},
    },

    mutations: {
        SET_PROYECTOS(state, data) {
            state.proyectos = data;
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
