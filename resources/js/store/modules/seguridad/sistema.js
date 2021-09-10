import { getSistemas } from './../partials/sistemas';

const sistemas = getSistemas();
const URI = '/api/SEGURIDAD_ERP/sistema/'
export default {
    namespaced: true,

    state: {
        sistemas: sistemas
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
        },
        leerAviso(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + "aviso/"+payload.id+"/leer", { params: payload.params })
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
        sistemas(state) {
            return state.sistemas;
        }
    }
}
