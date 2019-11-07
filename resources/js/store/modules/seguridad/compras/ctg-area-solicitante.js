const URI = '/api/SEGURIDAD_ERP/compras/ctg_area_solicitante/';


export default {
    namespaced: true,
    state: {
        area: [],
        currentArea: null,
        meta:{}
    },
    mutations:{
        SET_AREAS(state, data){
            state.areas = data;
        },
        SET_AREA(state, data){
            state.currentArea= data;
        },
        SET_META(state, data) {
            state.meta = data;
        },
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
        areas(state) {
            return state.areas;
        },
        meta(state) {
            return state.meta;
        },
        currentArea(state) {
            return state.currentArea;
        }
    }
}
