const URI = '/api/subcontrato/';

export default {
    namespaced: true,
    state: {
        subcontratos: [],
        currentSubcontrato: null,
    },

    mutations: {
        SET_SUBCONTRATOS(state, data) {
            state.subcontratos = data
        },

        SET_SUBCONTRATO(state, data) {
            state.currentSubcontrato = data
        }
    },

    actions: {
        find(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id, { params: payload.params })
                    .then(r => r.data)
                    .then((data) => {
                        context.commit('SET_SUBCONTRATO', data)
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        }
    },

    getters: {
        subcontratos(state) {
            return state.subcontratos
        },

        currentSubcontrato(state) {
            return state.currentSubcontrato
        }
    }
}