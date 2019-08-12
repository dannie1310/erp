const URI = '/api/SEGURIDAD_ERP/ctg_plaza/';


export default {
    namespaced: true,
    state: {
        plazas: [],
        currentPlaza: null,
        meta:{}
    },
    mutations:{
        SET_PLAZAS(state, data){
            state.plazas = data;
        },
        SET_PLAZA(state,data){
            state.currentPlaza = data;
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
        plazas(state) {
            return state.plazas;
        },
        currentPlaza(state) {
            return state.currentPlaza;
        }
    }
}
