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
        paginate (context, payload){
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'paginate', { params: payload.params }).then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
                            });
                    },
        store(context,payload){

            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Fondo",
                    text: "¿Estás seguro/a de que la información es correcta?",
                    icon: "info",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Registrar',
                            closeModal: false,
                        }
                    }                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post('/api/fondo/', payload)

                                .then(r => r.data)
                                .then(data => {
                                    swal("Fondo registrado correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    }).then(() => {
                                        resolve(data);
                                    })
                                })
                                .catch(error => {
                                    reject(error);
                                });
                        }
                    });
            });

        },
        storeResponsable(context,payload){
            return new Promise((resolve, reject) => {
                axios
                    .post('/api/empresa/', payload)
                    .then(r => r.data)
                    .then(data => {
                        resolve(data.id);
                    })
                    .catch(error => {
                        reject(error);
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