const URI = '/api/SEGURIDAD_ERP/rol/';

export default {
    namespaced: true,

    state: {
        roles: []
    },

    mutations: {
        SET_ROLES(state, data) {
            state.roles = data;
        }
    },

    actions: {
        index(context, payload = {}) {
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

        asignacionMasiva(context, payload = {}) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'asignacion-masiva', payload)
                    .then(data => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error =>  {
                        reject(error);
                    });
            });
        }
    },

    getters: {
        roles(state) {
            return state.roles
        }
    }
}