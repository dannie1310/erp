const URI = '/api/activo-fijo/resguardo/';

export default {
    namespaced: true,
    state: {
        activos: [],
        currentActivo: '',
        meta:{}
    },

    mutations: {
        SET_ACTIVOS(state, data) {
            state.activos = data;
        },
        SET_ACTIVO(state, data) {
            state.currentActivo = data;
        },
        SET_META(state, data) {
            state.meta = data;
        },

        UPDATE_ATTRIBUTE(state, data) {
            _.set(state.currentActivo, data.attribute, data.value);
        },

        UPDATE_ACTIVO(state, data) {
            state.activos = state.activos.map(ca => {
                if (ca.id === data.id) {
                    return Object.assign({}, ca, data)
                }
                return ca
            })
            state.currentActivo = data;
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
                    });
            });
        },
        getLista(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'lista', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    });
            })
        },
        getResguardos(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'getResguardos', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    });
            })
        },
    },

    getters: {
        activos(state) {
            return state.activos
        },
        currentActivo(state) {
            return state.currentActivo
        },
        meta(state) {
            return state.meta;
        },
    }
}
