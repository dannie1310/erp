const URI = '/api/compras/asignacion-compra/';

export default {
    namespaced: true,
    state: {
        asignaciones: [],
        asignacion: [],
        meta: {}
    },

    mutations: {
        SET_ASIGNACIONES(state, data) {
            state.asignaciones = data
        },

        SET_ASIGNACION(state, data) {
            state.asignacion = data
        },

        SET_META(state, data) {
            state.meta = data;
        }
    },

    actions: {
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
        }
    },

    getters: {
        asignaciones(state) {
            return state.solicitudes
        },

        asignacion(state) {
            return state.asignacion
        },

        meta(state) {
            return state.meta
        }
    }
}
