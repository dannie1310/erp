const URI = '/api/contratos/tipo-contrato/';
export default{
    namespaced: true,
    state: {
        tiposContrato: [],
        currentTipoContrato: null,
        meta: {}
    },
    mutations: {
        SET_TIPOSCONTRATOS(state, data){
            state.tiposContrato = data
        },

        SET_META(state, data){
            state.meta = data
        },

        SET_TIPOCONTRATO(state, data){
            state.currentTipoContrato = data
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
        paginate(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'paginate', {params: payload.params})
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
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
                    })
            });
        },
    },
    getters: {
        tiposContrato(state) {
            return state.tiposContrato
        },

        meta(state) {
            return state.meta
        },

        currentTipoContrato(state) {
            return state.currentTipoContrato
        }
    }

}