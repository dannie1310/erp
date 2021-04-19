const URI = '/api/acarreos/operador/';

export default{
    namespaced: true,
    state: {
        operadores: [],
        currentOperador: null,
        meta: {}
    },

    mutations: {
        SET_OPERADORES(state, data){
            state.operador = data
        },

        SET_META(state, data){
            state.meta = data
        },

        SET_OPERADOR(state, data){
            state.currentOperador = data
        }
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
                        reject(error);
                    });
            });
        },
    },

    getters: {
        operadores(state) {
            return state.operadores
        },

        meta(state) {
            return state.meta
        },

        currentOperador(state) {
            return state.currentOperador
        }
    }
}
