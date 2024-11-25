const URI = '/api/control-recursos/solicitud-comprobacion/';

export default {
    namespaced: true,
    state: {
        comprobaciones: [],
        currentComprobacion: null,
        meta: {}
    },

    mutations: {
        SET_COMPROBACIONES(state, data) {
            state.comprobaciones = data
        },
        SET_COMPROBACION(state, data)
        {
            state.currentComprobacion = data;
        },
        SET_META(state, data) {
            state.meta = data;
        },
        UPDATE_COMPROBACION(state, data) {
            state.comprobaciones = state.comprobaciones.map(c => {
                if (c.id === data.id) {
                    return Object.assign([], c, data)
                }
                return c
            })
            state.currentComprobacion = data
        },

        UPDATE_ATTRIBUTE(state, data) {
            state.currentComprobacion[data.attribute] = data.value
        },
        DELETE_COMPROBACION(state, id) {
            state.comprobaciones = state.comprobaciones.filter(d => {
                return d.id != id
            });
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
        comprobaciones(state) {
            return state.comprobaciones
        },
        meta(state) {
            return state.meta
        },
        currentComprobacion(state) {
            return state.currentComprobacion;
        }
    }
}
