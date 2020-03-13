const URI = '/api/control-presupuesto/tipo-orden/';

export default {
    namespaced: true,
    state: {
        tiposOrdenes: [],
        currentTipoOrden: null,
        meta: {},
    },

    mutations: {
        SET_ORDENES(state, data) {
            state.tiposOrdenes = data
        },
        SET_ORDEN(state, data) {
            state.currentTipoOrden = data;
        },
        SET_META(state, data) {
            state.meta = data;
        },
    },

    actions: {
        index(context, payload = {}) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI, {params : payload.params})
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data.data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
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
        find(context, payload) {
            return new Promise((resolve, reject) => {
                axios.get(URI + payload.id, { params: payload.params })
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
        tiposOrdenes(state) {
            return state.tiposOrdenes
        },
        currentTipoOrden(state) {
            return state.currentTipoOrden
        },
        meta(state) {
            return state.meta
        },
    }
}