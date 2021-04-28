const URI = '/api/acarreos/marca/';

export default{
    namespaced: true,
    state: {
        marcas: [],
        currentMarca: null,
        meta: {}
    },

    mutations: {
        SET_MARCAS(state, data){
            state.marcas = data
        },

        SET_META(state, data){
            state.meta = data
        },

        SET_MARCA(state, data){
            state.currentMarca = data
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
        marcas(state) {
            return state.marcas
        },

        meta(state) {
            return state.meta
        },

        currentMarca(state) {
            return state.currentMarca
        }
    }
}
