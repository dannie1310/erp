const URI = '/api/padron-proveedores/ctg-area/';

export default {
    namespaced: true,
    state: {
        areas: [],
        currentArea: null,
        meta: {}
    },

    mutations: {
        SET_AREAS(state, data) {
            state.areas = data;
        },

        SET_AREA(state, data) {
            state.currentArea = data;
        },

        SET_META(state, data) {
            state.meta = data;
        }
    },

    actions: {
        index(context, payload) {
            return new Promise((resolve, reject) => {

                axios
                    .get(URI, { params: payload.params })
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data.data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
    },
    getters: {
        areas(state) {
            return state.areas;
        },

        meta(state) {
            return state.meta;
        },

        currentArea(state) {
            return state.currentArea;
        }
    }
}