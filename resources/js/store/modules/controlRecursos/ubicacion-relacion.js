const URI = '/api/control-recursos/ubicacion-relacion/';

export default {
    namespaced: true,
    state: {
        ubicaciones: [],
        currentUbicacion: '',
        meta:{}
    },

    mutations: {
        SET_UBICACIONES(state, data) {
            state.ubicaciones = data;
        },
        SET_UBICACION(state, data) {
            state.currentUbicacion = data;
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
                        reject(error)
                    })
            });
        },
    },

    getters: {
        usuarioUbicaciones(state) {
            return state.usuarioUbicaciones
        },
        currentUbicacion(state) {
            return state.currentUbicacion
        },
        meta(state) {
            return state.meta;
        },
    }
}
