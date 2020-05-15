const URI = '/api/contabilidad-general/incidente-poliza/';
export default {
    namespaced: true,
    state: {
        incidentes: [],
        currentIncidente: null,
        meta: {},
    },

    mutations: {
        SET_INCIDENTES(state, data) {
            state.incidentes = data;
        },

        SET_META(state, data) {
            state.meta = data;
        },

        UPDATE_INCIDENTE(state, data) {
            state.incidentes = state.incidentes.map(incidente => {
                if (incidente.id === data.id) {
                    return Object.assign({}, incidente, data)
                }
                return incidente
            })
            state.currentIncidente = state.currentIncidente ? data : null;
        },

        SET_INCIDENTE(state, data) {
            state.currentIncidente = data;
        }
    },

    actions: {
        paginate (context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'paginate', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            })
        },

        find(context, payload) {
            return new Promise((resolve, reject) => {
                axios.get(URI + payload.id, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },

        buscar(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'buscar-diferencias', payload)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
    },

    getters: {
        incidentes(state) {
            return state.incidentes
        },

        meta(state) {
            return state.meta
        },

        currentIncidente(state) {
            return state.currentIncidente
        }
    }
}