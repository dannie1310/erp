const URI = '/api/control-presupuesto/tarjeta/';

export default {
    namespaced: true,
    state: {
        tarjetas: [],
        currentTarjeta: null,
        meta: {},
    },

    mutations: {
        SET_TARJETAS(state, data) {
            state.tarjetas = data
        },
        SET_TARJETA(state, data) {
            state.currentTarjeta = data;
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
        tarjetas(state) {
            return state.tarjetas
        },
        currentTarjeta(state) {
            return state.currentTarjeta
        },
        meta(state) {
            return state.meta
        },
    }
}