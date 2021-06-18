const URI = '/api/contratos/concepto/';

export default {
    namespaced: true,
    state: {
        conceptos: [],
        currentConcepto: null,
    },

    mutations: {
        SET_CONCEPTOS(state, data) {
            state.conceptos = data
        },
        SET_CONCEPTO(state, data) {
            state.currentConcepto = data;
        }
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
        find(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    });
            });
        },
    },

    getters: {
        conceptos(state) {
            return state.conceptos
        },
        currentConcepto(state) {
            return state.currentConcepto
        }
    }
}
