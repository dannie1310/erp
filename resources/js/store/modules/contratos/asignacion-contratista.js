const URI = '/api/contratos/asignacion-contratista/';
export default{
    namespaced: true,
    state: {
        asignaciones: [],
        currentAsignacion: null,
        meta: {}
    },
    mutations: {
        SET_ASIGNACIONES(state, data){
            state.asignaciones = data
        },

        SET_META(state, data){
            state.meta = data
        },

        SET_ASIGNACION(state, data){
            state.currentAsignacion = data
        },

        UPDATE_ASIGNACIONES(state, data) {
            state.asignaciones = state.asignaciones.map(inventario => {
                if (asignacion.id === data.id) {
                    return Object.assign({}, asignacion, data)
                }
                return asignacion
            })
            state.currentAsignacion != null ? data : null;
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
        getContratos(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'getContratos', {params: payload.params})
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },
        getCotizaciones(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id + '/getCotizaciones', {params: payload.params})
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
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
    },
    getters: {
        asignaciones(state) {
            return state.asignaciones
        },

        meta(state) {
            return state.meta
        },

        currentAsignacion(state) {
            return state.currentAsignacion
        }
    }

}