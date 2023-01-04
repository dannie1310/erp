const URI = '/api/seguimiento/moneda/';

export default {
    namespaced: true,
    state: {
        monedas: [],
        currentMoneda: null,
        meta: {},
    },

    mutations: {
        SET_MONEDAS(state, data) {
            state.monedas = data;
        },

        SET_MONEDA(state, data) {
            state.currentMoneda = data;
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
        monedas(state) {
            return state.monedas;
        },
        meta(state) {
            return state.meta;
        },
        currentMoneda(state) {
            return state.currentMoneda;
        },
    }
}
