const URI = '/api/almacen/';

export default {
    namespaced: true,
    state: {
        almacenes: []
    },

    mutations: {
        SET_ALMACENES(state, data) {
            state.almacenes = data;
        }
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
        // materiales(context, payload) {
        //     return new Promise((resolve, reject) => {
        //         axios
        //             .get(URI + payload.id +'/materiales', { params: payload.params })
        //             .then(r => r.data)
        //             .then(data => {
        //                 resolve(data);
        //             })
        //             .catch(error => {
        //                 reject(error)
        //             })
        //     });
        // },

    },

    getters: {
        almacenes(state) {
            return state.almacenes
        }
    }
}
