const URI = '/api/SEGURIDAD_ERP/permiso/';

export default {
    namespaced: true,

    state: {
        permisos: []
    },

    mutations: {
        SET_PERMISOS(state, data) {
            state.permisos = data;
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
        }
    },

    getters: {
        permisos(state) {
            return state.permisos
        }
    }
}