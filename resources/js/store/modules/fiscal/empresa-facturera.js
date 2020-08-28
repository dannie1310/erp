const URI = '/api/SEGURIDAD_ERP/empresa-facturera/';
export default {
    namespaced: true,
    state: {
        empresas_factureras: [],
        currentEmpresaFacturera: null,
        meta: {},
    },

    mutations: {
        SET_EMPRESAS_FACTURERAS(state, data) {
            state.empresas_factureras = data;
        },

        SET_META(state, data) {
            state.meta = data;
        },

        UPDATE_EMPRESA_FACTURERA(state, data) {
            state.empresas_factureras = state.empresas_factureras.map(incidente => {
                if (incidente.id === data.id) {
                    return Object.assign({}, incidente, data)
                }
                return incidente
            })
            state.currentEmpresaFacturera = state.currentEmpresaFacturera ? data : null;
        },

        SET_EMPRESA_FACTURERA(state, data) {
            state.currentEmpresaFacturera = data;
        }
    },

    actions: {
        index(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI, { params: payload.params })
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },

        buscar(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'buscar-coincidencias', payload)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
    },

    getters: {
        incidentes(state) {
            return state.empresas_factureras
        },

        meta(state) {
            return state.meta
        },

        currentIncidente(state) {
            return state.currentEmpresaFacturera
        }
    }
}