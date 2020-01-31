const URI = '/api/subcontratos-estimaciones/descuento/';
export default{
    namespaced: true,
    state: {
        descuentos: [],
        currentdescuento: null,
        meta: {}
    },
    mutations: {
        SET_DECUENTOS(state, data){
            state.descuentos = data
        },

        SET_META(state, data){
            state.meta = data
        },

        SET_DECUENTO(state, data){
            state.currentdescuento = data
        },

        // UPDATE_VENTAS(state, data) {
        //     state.descuentos = state.descuentos.map(descuento => {
        //         if (venta.id === data.id) {
        //             return Object.assign({}, venta, data)
        //         }
        //         return venta
        //     })
        //     state.currentVenta != null ? data : null;
        // }
    },
    actions: {
        find(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },
        list(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id + '/list', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },
        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Descuento de Material",
                    text: "¿Está seguro de que la información es correcta?",
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
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI, payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Descuento de Material registrado correctamente", {
                                        icon: "success",
                                        timer: 2000,
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
        descuentos(state) {
            return state.descuentos
        },

        meta(state) {
            return state.meta
        },

        currentdescuento(state) {
            return state.currentdescuento
        }
    }
}