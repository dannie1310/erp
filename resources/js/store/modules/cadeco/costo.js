const URI = '/api/costo/';

export default {
    namespaced: true,
    state: {
        costos: [],
        currentCosto: null,
        meta: {}
    },

    mutations: {
        SET_COSTOS(state, data) {
            state.costos = data
        },

        SET_COSTO(state, data) {
            state.currentCosto = data;
        },

        SET_META(state, data) {
            state.meta = data
        },

        UPDATE_COSTO(state, data) {
            state.costos = state.costos.map(costo => {
                if (costo.id === data.id) {
                    return Object.assign({}, costo, data)
                }
                return costo
            })
            state.currentCosto = data;
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
                    .get(URI + 'paginate', { params: payload })
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
        costos(state) {
            return state.costos
        },
        meta(state) {
            return state.meta
        }
    }
}