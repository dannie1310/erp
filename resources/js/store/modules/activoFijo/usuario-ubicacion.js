const URI = '/api/activo-fijo/usuario-ubicacion/';

export default {
    namespaced: true,
    state: {
        usuarioUbicaciones: [],
        currentUsuarioUbicacion: '',
        meta:{}
    },

    mutations: {
        SET_ACTIVOS(state, data) {
            state.usuarioUbicaciones = data;
        },
        SET_ACTIVO(state, data) {
            state.currentUsuarioUbicacion = data;
        },
        SET_META(state, data) {
            state.meta = data;
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
        getListaUbicaciones(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'listaUbicaciones', { params: payload.params })
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
        usuarioUbicaciones(state) {
            return state.usuarioUbicaciones
        },
        currentUsuarioUbicacion(state) {
            return state.currentUsuarioUbicacion
        },
        meta(state) {
            return state.meta;
        },
    }
}
