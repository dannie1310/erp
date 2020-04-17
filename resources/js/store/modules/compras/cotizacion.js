const URI = '/api/compras/cotizacion/';

export default {
    namespaced: true,
    state: {
        cotizaciones: [],
        meta: {}
    },

    mutations: {
        SET_COTIZACIONES(state, data) {
            state.cotizaciones = data
        },

        SET_META(state, data) {
            state.meta = data;
        }
    },

    actions: {
        paginate (context, payload) {
            console.log('paginate cotizaciones', payload);
            
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'paginate', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            })
        },
        store(context,payload){
            console.log(payload);
            
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Cotización de Compra",
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
                                .post(URI, payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Cotización de Compra registrada correctamente", {
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

        }
    },

    getters: {
        cotizaciones(state) {
            return state.cotizaciones
        },

        meta(state) {
            return state.meta
        }
    }
}
