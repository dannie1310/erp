const URI = '/api/control-recursos/documento/';

export default {
    namespaced: true,
    state: {
        documentos: [],
        currentDocumento: null,
        meta: {}
    },

    mutations: {
        SET_DOCUMENTOS(state, data) {
            state.documentos = data
        },
        SET_DOCUMENTO(state, data)
        {
            state.currentDocumento = data;
        },
        SET_META(state, data) {
            state.meta = data;
        },
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
    },

    getters: {
        documentos(state) {
            return state.documentos
        },
        meta(state) {
            return state.meta
        },
        currentDocumento(state) {
            return state.currentDocumento;
        }
    }
}
