const URI = '/api/fiscal/notificacion_rep/';
export default {
    namespaced: true,
    state: {
        notificaciones: [],
        currentNotificacion: null,
        meta: {},
    },

    mutations: {
        SET_NOTIFICACIONES(state, data) {
            state.notificaciones = data;
        },

        SET_META(state, data) {
            state.meta = data;
        },

        SET_NOTIFICACION(state, data) {
            state.currentNotificacion = data;
        }
    },

    actions: {
        paginate (context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'paginate', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        context.commit("SET_NOTIFICACIONES", data.data);
                        context.commit("SET_META", data.meta);
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            })
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
    },

    getters: {
        notificaciones(state) {
            return state.notificaciones
        },

        meta(state) {
            return state.meta
        },

        currentNotificacion(state) {
            return state.currentNotificacion
        }
    }
}
