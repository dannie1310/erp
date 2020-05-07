const URI = '/api/compras/cotizacion/';

export default {
    namespaced: true,
    state: {
        cotizaciones: [],
        currentCotizacion: null,
        meta: {}
    },

    mutations: {
        SET_COTIZACIONES(state, data) {
            state.cotizaciones = data
        },

        SET_META(state, data) {
            state.meta = data;
        },

        SET_COTIZACION(state, data) {
            state.currentCotizacion = data;
        }
    },

    actions: {
        paginate (context, payload) {
            
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
        descargaLayout(context, payload){
            var urr = URI + 'descargaLayout/'+ payload.id +'?db=' + this._vm.$session.get('db') + '&idobra=' + this._vm.$session.get('id_obra') + '&access_token=' + this._vm.$session.get('jwt');
            var win = window.open(urr, "_blank");

            win.onbeforeunload = () => {
                swal("Layout descargado correctamente.", {
                    icon: "success",
                    timer: 2000,
                    buttons: false
                })
            }
        },    
        update(context, payload)
        {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Estás seguro?",
                    text: "Actualizar Cotización",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Actualizar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .patch(URI + payload.id, payload.post)
                                .then(r => r.data)
                                .then(data => {
                                    swal("La Cotización se ha actualizado correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    })
                                        .then(() => {
                                            resolve(data);
                                        })
                                })
                                .catch(error => {
                                    reject(error);
                                })
                        }
                    });
            });
        },
        store(context,payload){
            
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
        },
        
        currentCotizacion(state) {
            return state.currentCotizacion;
        }
    }
}
