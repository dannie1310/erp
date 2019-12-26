const URI = '/api/ventas/venta/';
export default{
    namespaced: true,
    state: {
        ventas: [],
        currentVenta: null,
        meta: {}
    },

    mutations: {
        SET_VENTAS(state, data){
            state.ventas = data
        },

        SET_META(state, data){
            state.meta = data
        },

        SET_VENTA(state, data){
            state.currentVenta = data
        },

        UPDATE_VENTAS(state, data) {
            state.ventas = state.ventas.map(venta => {
                if (venta.id === data.id) {
                    return Object.assign({}, venta, data)
                }
                return venta
            })
            state.currentVenta != null ? data : null;
        }
    },

    actions: {
        delete(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Cancelar Venta",
                    text: "¿Está seguro/a de que desea cancelar la venta?",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Cancelar',
                            closeModal: false,
                        }
                    },
                    dangerMode: true,
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .delete(URI + payload.id, { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Venta cancelada correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    }).then(() => {
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

        index(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
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
                    .get(URI + 'paginate', {params: payload.params})
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
                    title: "Registrar Venta de Insumo",
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
                                    swal("Venta registrada correctamente", {
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
        ventas(state) {
            return state.ventas
        },

        meta(state) {
            return state.meta
        },

        currentVenta(state) {
            return state.currentVenta
        }
    }
}
