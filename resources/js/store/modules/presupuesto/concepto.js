const URI = '/api/presupuesto/concepto/';
export default {
    namespaced: true,
    state: {
        conceptos: [],
        currentConcepto: null,
        meta: {},
    },

    mutations: {
        SET_CONCEPTOS(state, data) {
            state.conceptos = data;
        },
        SET_META(state, data) {
            state.meta = data;
        },
        SET_CONCEPTO(state, data) {
            state.currentConcepto = data;
        },
    },

    actions: {
        list(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.nivel_padre, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        context.commit("SET_CONCEPTOS", data.data);
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
        meta(state) {
            return state.meta
        },
        currentConcepto(state) {
            return state.currentConcepto
        }
    }
}
