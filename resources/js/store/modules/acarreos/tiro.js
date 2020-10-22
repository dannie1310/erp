const URI = '/api/acarreos/tiro/';

export default {
    namespaced: true,
    state: {
        tiros: [],
        currentTiro: '',
        meta:{}
    },

    mutations: {
        SET_TIROS(state, data) {
            state.tiros = data;
        },
        SET_TIRO(state, data) {
            state.currentTiro = data;
        },
        SET_META(state, data) {
            state.meta = data;
        },
    },

    actions: {
        index(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    });
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
                        reject(error);
                    });
            });
        },
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
    },

    getters: {
        tiros(state) {
            return state.tiros
        },
        currentTiro(state) {
            return state.currentTiro
        },
        meta(state) {
            return state.meta;
        },

    }
}
