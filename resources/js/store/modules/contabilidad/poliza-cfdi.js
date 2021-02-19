const URI = '/api/contabilidad/poliza-cfdi/';
export default {
    namespaced: true,
    state: {
        polizas: [],
        currentPoliza: null,
        meta: {},
    },

    mutations: {
        SET_POLIZAS(state, data) {
            state.polizas = data;
        },

        SET_META(state, data) {
            state.meta = data;
        },

        UPDATE_POLIZA(state, data) {
            state.polizas = state.polizas.map(poliza => {
                if (poliza.id === data.id) {
                    return Object.assign({}, poliza, data)
                }
                return poliza
            })
            state.currentPoliza = state.currentPoliza ? data : null;
        },

        SET_POLIZA(state, data) {
            state.currentPoliza = data;
        }
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

        find(context, payload) {
            return new Promise((resolve, reject) => {
                axios.get(URI + payload.id, { params: payload.params })
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
        polizas(state) {
            return state.polizas
        },

        meta(state) {
            return state.meta
        },

        currentPoliza(state) {
            return state.currentPoliza
        }
    }
}
