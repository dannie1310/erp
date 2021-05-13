const URI = '/api/remesas/folio/';

export default {
    namespaced: true,
    state: {
        folios: [],
        currentFolio: '',
        meta:{}
    },

    mutations: {
        SET_FOLIOS(state, data) {
            state.folios = data
        },
        SET_FOLIO(state, data) {
            state.currentFolio = data;
        },
        SET_META(state, data) {
            state.meta = data;
        },
    },

    actions: {
        paginate(context, payload) {
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
            })
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
                        reject(error);
                    })
            });
        },
    },

    getters: {
        folios(state) {
            return state.folios;
        },
        currentFolio(state) {
            return state.currentFolio;
        },
        meta(state) {
            return state.meta;
        },
    }
}
