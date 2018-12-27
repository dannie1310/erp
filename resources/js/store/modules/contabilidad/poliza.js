const URI = '/api/contabilidad/poliza/';
export default {
    namespaced: true,
    state: {
        polizas: [],
        meta: {}
    },

    mutations: {
        fetch(state, payload) {
            state.polizas = payload.data;
            state.meta = payload.meta
        },

        update(state, payload) {
            state.polizas = state.polizas.map(poliza => {
                if (poliza.id === payload.id) {
                    return Object.assign({}, poliza, payload.data)
                }
                return poliza
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
        polizas(state) {
            return state.polizas
        },

        meta(state) {
            return state.meta
        }
    }
}