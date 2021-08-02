const URI = '/api/padron-proveedores/invitacion/';

export default {
    namespaced: true,
    state: {
        invitaciones: [],
        currentInvitacion: null,
        meta: {}
    },

    mutations: {
        SET_INVITACIONES(state, data) {
            state.invitaciones = data;
        },

        SET_INVITACION(state, data) {
            state.currentInvitacion = data;
        },

        SET_META(state, data) {
            state.meta = data;
        }
    },

    actions: {
        paginate(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'paginate', {params: payload.params})
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },
        getSolicitudes(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'getSolicitudes', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    });
            });
        },
        getSolicitud(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id+'/getSolicitud', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
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
                        reject(error)
                    })
            });
        },
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
        invitaciones(state) {
            return state.invitaciones;
        },

        meta(state) {
            return state.meta;
        },

        currentInvitacion(state) {
            return state.currentInvitacion;
        }
    }
}
