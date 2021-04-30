const URI = '/api/acarreos/sindicato/';

export default{
    namespaced: true,
    state: {
        sindicatos: [],
        currentSindicato: null,
        meta: {}
    },

    mutations: {
        SET_SINDICATOS(state, data){
            state.sindicatos = data
        },

        SET_META(state, data){
            state.meta = data
        },

        SET_SINDICATO(state, data){
            state.currentSindicato = data
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
        sindicatos(state) {
            return state.sindicatos
        },

        meta(state) {
            return state.meta
        },

        currentSindicato(state) {
            return state.currentSindicato
        }
    }
}
