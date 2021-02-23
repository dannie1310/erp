const URI = '/api/padron-proveedores/giro/';

export default {
    namespaced: true,
    state: {
        giros: [],
        currentGiro: null,
        meta: {}
    },

    mutations: {
        SET_GIROS(state, data) {
            state.giros = data;
        },

        SET_GIRO(state, data) {
            state.currentGiro = data;
        },

        SET_META(state, data) {
            state.meta = data;
        }
    },

    actions: {
        find(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    });
            });
        },
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
        giros(state) {
            return state.giros;
        },

        meta(state) {
            return state.meta;
        },

        currentGiro(state) {
            return state.currentGiro;
        }
    }
}
