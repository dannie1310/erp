const URI = '/api/padron-proveedores/archivo-proveedora/';

export default {
    namespaced: true,
    state: {
        archivos: [],
        currentArchivo: null,
        meta: {}
    },

    mutations: {
        SET_ARCHIVOS(state, data) {
            state.archivos = data;
        },

        SET_ARCHIVO(state, data) {
            state.currentArchivo = data;
        },

        SET_META(state, data) {
            state.meta = data;
        },

        UPDATE_ARCHIVO(state, data) {
            state.archivos = state.archivos.map(archivo => {
                if (archivo.id === data.id) {
                    return Object.assign({}, archivo, data)
                }
                return archivo
            })
            if (state.currentArchivo) {
                state.currentArchivo = data
            }
        },
    },

    actions: {
    },
    getters: {
        archivos(state) {
            return state.archivos;
        },

        meta(state) {
            return state.meta;
        },

        currentArchivo(state) {
            return state.currentArchivo;
        }
    }
}