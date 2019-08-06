const URI = '/api/finanzas/fondo/';


export default {
    namespaced: true,
    state: {
        fondos: [],
        currentFondo: null,
        meta:{}
    },
    mutations:{
      SET_FONDOS(state, data){
        state.fondos = data;
      },
      SET_FONDO(state,data){
          state.currentFondo = data;
      },
      SET_META(state,meta){
          state.meta = data;
      }
    },
    actions: {
       ctgTipoFondo(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI +'tipo-fondo', { params: payload.params })
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        currentFondo(state){
            return state.currentFondo
        },
        meta(state){
            return state.meta
        },

    },

    getters: {
        fondos(state) {
            return state.fondos;
        },
        currentFondo(state) {
            return state.currentFondo;
        }
    }
}