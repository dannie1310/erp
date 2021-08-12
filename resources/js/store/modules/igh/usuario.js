const URI = '/api/IGH/usuario/'
export default {
    namespaced: true,

    state: {
        usuarios: []
    },

    mutations: {
        SET_USUARIOS(state, data) {
            state.usuarios = data;
        }
    },

    actions: {
        index(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI, { params: payload.params })
                    .then(r => r.data)
                    .then((data) => {

                        resolve(data.data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },

        find(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id, payload.config)
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },

        findPorCorreo(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI +'por-correo/'+ payload.correo, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },

        findPorCorreos(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI +'por-correos/', payload.data, payload.config)
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },

        currentUser(context, payload = {}) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'currentUser', payload.config)
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        }
    },

    getters: {
        usuarios(state) {
            return state.usuarios;
        }
    }
}
