/**
 * Created by Hermes on 26/02/2019.
 */
const URI = '/api/SEGURIDAD_ERP/sistema/'
export default {
    namespaced: true,

    state: {
        sistemas:[]
    },

    mutations: {
        SET_SISTEMAS(state, data) {
            state.sistemas = data;
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
        sistemas(state) {
            return state.sistemas;
        }
    }
}