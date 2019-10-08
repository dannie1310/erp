const URI = '/api/almacenes/tipo-conteo/';
export default{
    namespaced: true,
    state: {
        conteos: [],
        currentConteo: null,
        meta: {}
    },

    mutations: {
        SET_CONTEOS(state, data){
            state.conteos = data
        },

        SET_META(state, data){
            state.meta = data
        },

        SET_CONTEO(state, data){
            state.currentConteo = data
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
        find(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI  + payload.id, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },
    },

    getters: {
        conteos(state) {
            return state.conteos
        },

        meta(state) {
            return state.meta
        },

        currentConteo(state) {
            return state.currentConteo
        }
    }
}