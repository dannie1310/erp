const URI = '/api/compras/orden-compra/';

export default {
    namespaced: true,
    state: {
        ordenes: [],
        currentOrden: null,
        meta: {}
    },

    mutations: {
        SET_ORDENES(state, data) {
            state.ordenes = data
        },
        SET_ORDEN(state, data) {
            state.currentOrden = data;
        },
        SET_META(state, data) {
            state.meta = data
        },
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
        }
    },

    getters: {
        ordenes(state) {
            return state.ordenes
        },
        currentOrden(state) {
            return state.currentOrden
        },
        meta(state) {
            return state.meta
        },
    }
}