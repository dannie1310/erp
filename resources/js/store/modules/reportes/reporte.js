const URI = '/api/reportes/';
export default {
    namespaced: true,
    state: {
        reportes: [],
        currentReporte: null,
        meta: {},
    },

    mutations: {
        SET_REPORTES(state, data) {
            state.reportes = data;
        },

        SET_META(state, data) {
            state.meta = data;
        },

        UPDATE_REPORTE(state, data) {
            state.reportes = state.reportes.map(reporte => {
                if (reporte.id === data.id) {
                    return Object.assign({}, reporte, data)
                }
                return reporte
            })
            state.currentReporte = state.currentReporte ? data : null;
        },

        SET_REPORTE(state, data) {
            state.currentReporte = data;
        }
    },

    actions: {
        paginate(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'paginate', {params: payload.params})
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
                axios.get(URI + payload.id, {params: payload.params})
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
        reportes(state) {
            return state.reportes
        },

        meta(state) {
            return state.meta
        },

        currentReporte(state) {
            return state.currentReporte
        }
    }
}