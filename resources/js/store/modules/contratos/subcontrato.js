const URI = '/api/contratos/subcontrato/';

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
            state.currentSubcontrato = data;
        }
    },

    actions: {
        index(context, payload = {}) {
            return new Promise((resolve, reject) => {

                axios
                    .get(URI, payload.config)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data.data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },

        find (context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI+payload.id, {params: payload.params})
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },

        getConceptosNuevaEstimacion(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id + '/getConceptosNuevaEstimacion')
                    .then(r => r.data)
                    .then(data => {
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