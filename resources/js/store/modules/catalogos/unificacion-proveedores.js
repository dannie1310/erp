const URI = '/api/catalogos/unificacion-proveedores/';

export default {
    namespaced: true,
    state: {
        unificacionProveedores: [],
        currentUnificacion: '',
    },

    mutations: {
        SET_UNIFICACIONES(state, data) {
            state.unificacionProveedores = data;
        },
        SET_UNIFICACION(state, data) {
            state.currentUnificacion = data;
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
        unificacionProveedores(state) {
            return state.unificacionProveedores
        },
        currentUnificacion(state) {
            return state.currentUnificacion
        },
        meta(state) {
            return state.meta;
        },

    }
}
