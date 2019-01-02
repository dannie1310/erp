const URI = '/api/contabilidad/poliza/';
export default {
    namespaced: true,
    state: {
        polizas: [],
        estatus: [],
        tiposPolizaInterfaz: [],
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
        },
        setEstatus(state, payload) {
            state.estatus = payload.data;
        },
        setTiposPolizaInterfaz(state, payload) {
            state.tiposPolizaInterfaz = payload.data;
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

        getEstatus(context){
            return new Promise((resolve, reject) => {
                axios.get(URI + 'estatus_prepoliza')
                    .then(res => {
                        resolve(res.data)
                    })
                    .catch(err => {
                        reject(err)
                    })
            }).then(res => {
                context.commit('setEstatus', res)
            })
        },

        getTiposPolizaInterfaz(context) {
            return new Promise((resolve, reject) => {
                axios.get(URI + 'tipo_poliza_contpaq')
                    .then(res => {
                        resolve(res.data)
                    })
                    .catch(err => {
                        reject(err)
                    })
            }).then(res => {
                context.commit('setTiposPolizaInterfaz', res)
            })
        }
    },

    getters: {
        polizas(state) {
            return state.polizas
        },

        meta(state) {
            return state.meta
        },
        estatus(state) {
            return state.estatus
        },
        tiposPolizaInterfaz(state) {
            return state.tiposPolizaInterfaz
        }
    }
}