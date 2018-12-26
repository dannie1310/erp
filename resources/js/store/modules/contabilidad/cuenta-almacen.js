const URI = '/api/contabilidad/cuenta-almacen/';
export default {
    namespaced: true,
    state: {
        cuentas: [],
        meta: {}
    },

    mutations: {
        fetch(state, payload) {
            state.cuentas = payload.data;
            state.meta = payload.meta
        },

        update(state, payload) {
            state.cuentas = state.cuentas.map(cuenta => {
                if (cuenta.id === payload.id) {
                    return Object.assign({}, cuenta, payload.data)
                }
                return cuenta
            })
        }
    },

    actions: {
        fetch (context, payload){
            axios.get(URI, {params: payload})
                .then(res => {
                    context.commit('fetch', res.data)
                })
                .catch(err => {
                    alert(err);
                });
        },

        find(context, id) {
            return new Promise((resolve, reject) => {
                axios.get(URI + id)
                    .then(res => {
                        resolve(res.data)
                    })
                    .catch(err => {
                        reject(err)
                    })
            })
        },

        update(context, payload) {
            return new Promise((resolve, reject) => {
                axios.patch(URI + payload.id, payload)
                    .then(response => {
                        context.commit('update', {id: payload.id, data: response.data.data});
                        resolve(response.data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            })
        }
    },

    getters: {
        cuentas(state) {
            return state.cuentas
        },

        meta(state) {
            return state.meta
        }
    }
}