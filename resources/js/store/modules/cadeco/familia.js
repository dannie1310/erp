const URI = '/api/familia/';


export default {
    namespaced: true,
    state: {
        familias: [],
        currentFamilia: null,
        meta: {}
    },

    mutations: {
        SET_FAMILIAS(state, data) {
            state.familias = data
        },

        SET_FAMILIA(state, data) {
            state.currentFamilia = data;
        },

        SET_META(state, data) {
            state.meta = data
        },
    },

    actions: {
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

        index(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
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
        }
    },

    getters: {
        familias(state) {
            return state.familias
        },
        meta(state) {
            return state.meta
        }
    }
}
