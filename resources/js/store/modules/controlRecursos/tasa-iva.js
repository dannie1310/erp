const URI = '/api/control-recursos/tasa-iva/';

export default {
    namespaced: true,
    state: {
        tasas: [],
        currentTasa: null,
        meta: {}
    },

    mutations: {
        SET_TASAS(state, data) {
            state.tasas = data
        },
        SET_TASA(state, data)
        {
            state.currentTasa = data;
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
        tasas(state) {
            return state.tasas
        },
        meta(state) {
            return state.meta
        },
        currentTasa(state) {
            return state.currentTasa;
        }
    }
}
