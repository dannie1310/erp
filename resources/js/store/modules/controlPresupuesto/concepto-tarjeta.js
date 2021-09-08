const URI = '/api/control-presupuesto/concepto-tarjeta/';

export default {
    namespaced: true,
    state: {
        conceptosTarjeta: [],
        currentConceptoTarjeta: null,
        meta: {},
    },

    mutations: {
        SET_CONCEPTOS_TARJETA(state, data) {
            state.conceptosTarjeta = data
        },
        SET_CONCEPTO_TARJETA(state, data) {
            state.currentConceptoTarjeta = data;
        },
        SET_META(state, data) {
            state.meta = data;
        },
    },

    actions: {
        index(context, payload = {}) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI, {params : payload.params})
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data.data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
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
        conceptosTarjeta(state) {
            return state.conceptosTarjeta
        },
        currentConceptoTarjeta(state) {
            return state.currentConceptoTarjeta
        },
        meta(state) {
            return state.meta
        },
    }
}