const URI = '/api/unidad/';


export default {
    namespaced: true,
    state: {
        unidades: [],
        currentUnidad: null,
        meta:{}
    },
    mutations:{
        SET_UNIDADES(state, data){
            state.unidades = data;
        },
        SET_UNIDAD(state,data){
            state.currentUnidad = data;
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
        }
    },

    getters: {
        unidades(state) {
            return state.unidades;
        },
        currentUnidad(state) {
            return state.currentUnidad;
        }
    }
}
