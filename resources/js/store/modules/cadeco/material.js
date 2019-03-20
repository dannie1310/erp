const URI = '/api/material/';

export default {
    namespaced: true,
    state: {
        materiales: [],
        currentMaterial: null,
        meta: {}
    },

    mutations: {
        SET_MATERIALES(state, data) {
            state.materiales = data
        },

        SET_MATERIAL(state, data) {
            state.currentMaterial = data;
        },

        SET_META(state, data) {
            state.meta = data
        },

        UPDATE_MATERIAL(state, data) {
            state.materiales = state.materiales.map(material => {
                if (material.id === data.id) {
                    return Object.assign({}, material, data)
                }
                return material
            })
            state.currentMaterial = state.currentMaterial ? data : null;
        },
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
                        reject(error)
                    })
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
                        reject(error)
                    })
            });
        },

        paginate(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'paginate', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            })
        }
    },

    getters: {
        materiales(state) {
            return state.materiales
        },
        meta(state) {
            return state.meta
        }
    }
}