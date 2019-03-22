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
        }
    },

    getters: {
        usuarios(state) {
            return state.usuarios;
        }
    }
}