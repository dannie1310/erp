const URI = '/api/compras/almacen/salida/';

export default {
    namespaced: true,
    state: {
        salidas: [],
        currentSalida: null,
        meta: {}
    },

    mutations: {
        SET_SALIDAS(state, data) {
            state.salidas = data
        },
        SET_SALIDA(state, data) {
            state.currentSalida = data;
        },
        SET_META(state, data) {
            state.meta = data
        },
    },

    actions: {
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
        salidas(state) {
            return state.salidas
        },
        currentSalida(state) {
            return state.currentSalida
        },
        meta(state) {
            return state.meta
        },
    }
}