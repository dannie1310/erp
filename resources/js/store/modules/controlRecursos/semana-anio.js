const URI = '/api/control-recursos/semana-anio/';

export default {
    namespaced: true,
    state: {
        semanas: [],
        currentSemana: null,
        meta: {}
    },

    mutations: {
        SET_SEMANAS(state, data) {
            state.semanas = data
        },
        SET_SEMANA(state, data)
        {
            state.currentSemana = data;
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
        semanas(state) {
            return state.semanas
        },
        meta(state) {
            return state.meta
        },
        currentSemana(state) {
            return state.currentSemana;
        }
    }
}
