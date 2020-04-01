const URI = '/api/contratos/subcontrato/';

export default {
    namespaced: true,
    state: {
        subcontratos: [],
        currentSubcontrato: null,
        meta: {}
    },

    mutations: {
        SET_SUBCONTRATOS(state, data) {
            state.subcontratos = data
        },
        SET_SUBCONTRATO(state, data) {
            state.currentSubcontrato = data;
        },
        SET_META(state, data) {
            state.meta = data
        },
    },

    actions: {
        index(context, payload) {
            return new Promise((resolve, reject) => {

                axios
                    .get(URI, { params: payload.params })
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

        ordenarConceptos (context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id+'/ordenarConceptos', { params: payload.params })
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        paginate (context, payload){
            
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'paginate', { params: payload.params })
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
        },
        meta(state) {
            return state.meta
        }
    }
}
