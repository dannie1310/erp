const URI = '/api/SEGURIDAD_ERP/permiso/';

export default {
    namespaced: true,

    state: {
        permisos: [],
        meta: {}
    },

    mutations: {
        SET_PERMISOS(state, data) {
            state.permisos = data;
        },

        SET_META(state, data) {
            state.meta = data;
        }
    },

    actions: {
        index(context, payload = {}) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI, payload.config)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    });
            });
        },

        porUsuario(context, id) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'por-usuario/' + id)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    });
            });
        },

        porObra(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'por-obra/' + payload.id, {params: payload.params})
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    });
            });
        },

    },

    getters: {
        permisos(state) {
            return state.permisos
        },

        meta(state) {
            return state.meta;
        }
    }
}