const URI = '/api/seguimiento/ingreso-partida/';

export default {
    namespaced: true,
    state: {
        partidas: [],
        currentPartida: null,
        meta: {},
    },

    mutations: {
        SET_PARTIDAS(state, data) {
            state.partidas = data;
        },

        SET_PARTIDA(state, data) {
            state.currentPartida = data;
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
        partidas(state) {
            return state.partidas;
        },
        meta(state) {
            return state.meta;
        },
        currentPartida(state) {
            return state.currentPartida;
        },
    }
}
