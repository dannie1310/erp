const URI = '/api/control-presupuesto/variacion-volumen/';

export default {
    namespaced: true,
    state: {
        variacionesVolumen: [],
        currentVariacion: null,
        meta: {},
    },

    mutations: {
        SET_VARIACIONES(state, data) {
            state.variacionesVolumen = data
        },
        SET_VARIACION(state, data) {
            state.currentVariacion = data;
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
        variacionesVolumen(state) {
            return state.variacionesVolumen
        },
        currentVariacion(state) {
            return state.currentVariacion
        },
        meta(state) {
            return state.meta
        },
    }
}