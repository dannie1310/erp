const URI = '/api/SEGURIDAD_ERP/ctg_banco/';


export default {
    namespaced: true,
    state: {
        bancos: [],
        currentBanco: null,
        meta:{}
    },
    mutations:{
        SET_BANCOS(state, data){
            state.bancos = data;
        },
        SET_BANCO(state,data){
            state.currentBanco = data;
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
        currentBanco(state){
            return state.currentBanco
        },
        meta(state){
            return state.meta
        },

    },

    getters: {
        bancos(state) {
            return state.bancos;
        },
        currentBanco(state) {
            return state.currentBanco;
        }
    }
}
