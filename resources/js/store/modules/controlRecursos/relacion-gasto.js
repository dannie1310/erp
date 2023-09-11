const URI = '/api/control-recursos/relacion-gasto/';

export default {
    namespaced: true,
    state: {
        relaciones: [],
        currentRelacion: null,
        meta: {}
    },

    mutations: {
        SET_RELACIONES(state, data) {
            state.relaciones = data
        },
        SET_RELACION(state, data)
        {
            state.currentRelacion = data;
        },
        SET_META(state, data) {
            state.meta = data;
        },
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
    },

    getters: {
        relaciones(state) {
            return state.relaciones
        },
        meta(state) {
            return state.meta
        },
        currentRelacion(state) {
            return state.currentRelacion;
        }
    }
}
