const URI = '/api/padron-proveedores/especialidad/';

export default {
    namespaced: true,
    state: {
        especialidades: [],
        currentEspecialidad: null,
        meta: {}
    },

    mutations: {
        SET_ESPECIALIDADES(state, data) {
            state.especialidades = data;
        },

        SET_ESPECIALIDAD(state, data) {
            state.currentEspecialidad = data;
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
        especialidades(state) {
            return state.especialidades;
        },

        meta(state) {
            return state.meta;
        },

        currentEspecialidad(state) {
            return state.currentEspecialidad;
        }
    }
}
