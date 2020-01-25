const URI = '/api/SEGURIDAD_ERP/incidencia/';


export default {
    namespaced: true,
    state: {
        incidencias: [],
        currentIncidencia: null,
        meta:{}
    },
    mutations:{
        SET_INCIDENCIAS(state, data){
            state.incidencias = data;
        },
        SET_INCIDENCIA(state,data){
            state.currentIncidencia = data;
        },
        SET_META(state,meta){
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
                    });
            });
        },
    },

    getters: {
        incidencias(state) {
            return state.incidencias;
        },
        currentIncidencia(state) {
            return state.currentIncidencia;
        }
    }
}
