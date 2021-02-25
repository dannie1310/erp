const URI = '/api/fiscal/no-localizado/';
export default {
    namespaced: true,
    state: {
        no_localizados: [],
        currentNoLocalizado: null,
        meta: {},
    },
    mutations: {
        SET_NO_LOCALIZADOS(state, data) {
            state.no_localizados = data;
        },
        SET_META(state, data) {
            state.meta = data;
        },
        SET_NO_LOCALIZADO(state, data) {
            state.currentNoLocalizado = data;
        },
    },
    actions: {
        paginate (context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'paginate', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            })
        },
    },

    getters: {
        no_localizados(state) {
            return state.no_localizados
        },

        meta(state) {
            return state.meta
        },

        currentNoLocalizado(state) {
            return state.currentNoLocalizado
        }
    }
}
