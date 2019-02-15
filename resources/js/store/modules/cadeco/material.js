const URI = '/api/material/';

export default {
    namespaced: true,
    state: {
        materiales: [],
        currentMaterial: null,
    },

    mutations: {
        SET_MATERIALES(state, data) {
            state.materiales = data
        },

        SET_MATERIAL(state, data) {
            state.currentMaterial = data;
        }
    },

    actions: {
        find(context, payload) {
            return new Promise((resolve, reject) => {
                context.commit('SET_MATERIAL', null)
                axios
                    .get(URI + payload.id, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        context.commit('SET_MATERIAL', data)
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },

        index(context, payload) {
            return new Promise((resolve, reject) => {
                context.commit('SET_MATERIALES', null)
                axios
                    .get(URI, { params: payload.params })
                    .then(r => r.data)
                    .then((data) => {
                        context.commit('SET_MATERIALES', data.data)
                        resolve(data.data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        }
    },

    getters: {
        materiales(state) {
            return state.materiales
        }
    }
}