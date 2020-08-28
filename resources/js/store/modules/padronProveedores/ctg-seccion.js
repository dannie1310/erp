const URI = '/api/padron-proveedores/ctg-seccion/';

export default {
    namespaced: true,
    state: {
        secciones: [],
        currentSeccion: null,
        meta: {}
    },

    mutations: {
        SET_SECCIONES(state, data) {
            state.secciones = data;
        },

        SET_SECCION(state, data) {
            state.currentSeccion = data;
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
        secciones(state) {
            return state.secciones;
        },

        meta(state) {
            return state.meta;
        },

        currentSeccion(state) {
            return state.currentSeccion;
        }
    }
}