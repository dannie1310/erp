const URI = '/api/activo-fijo/ubicacionResguardo/';

export default {
    namespaced: true,
    state: {
        activos: [],
        currentActivo: '',
        meta:{}
    },

    mutations: {
        SET_ACTIVOS(state, data) {
            state.activos = data;
        },
        SET_ACTIVO(state, data) {
            state.currentActivo = data;
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
                        reject(error);
                    });
            });
        },
    },

    getters: {
        activos(state) {
            return state.activos
        },
        currentActivo(state) {
            return state.currentActivo
        },
        meta(state) {
            return state.meta;
        },
    }
}
