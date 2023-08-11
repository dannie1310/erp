const URI = '/api/control-recursos/serie/';

export default {
    namespaced: true,
    state: {
        series: [],
        currentSerie: null,
        meta: {}
    },

    mutations: {
        SET_SERIES(state, data) {
            state.series = data
        },
        SET_SERIE(state, data)
        {
            state.currentSerie = data;
        },
        SET_META(state, data) {
            state.meta = data;
        },
    },

    actions: {
        index(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
    },

    getters: {
        series(state) {
            return state.series
        },
        meta(state) {
            return state.meta
        },
        currentSerie(state) {
            return state.currentSerie;
        }
    }
}
