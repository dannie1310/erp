const URI = '/api/seguimiento/factura/';

export default {
    namespaced: true,
    state: {
        facturas: [],
        currentFactura: null,
        meta: {},
        items_revision:[]
    },

    mutations: {
        SET_FACTURAS(state, data) {
            state.facturas = data;
        },

        SET_FACTURA(state, data) {
            state.currentFactura = data;
        },

        SET_META(state, data) {
            state.meta = data;
        },
    },

    actions: {
        paginate(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'paginate', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
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
        cancelar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Cancelar Factura",
                    text: "¿Está seguro de cancelar la factura?",
                    dangerMode: true,
                    icon: "info",
                    content: {
                        element: "input",
                        attributes: {
                            placeholder: "Motivo de Cancelación",
                            type: "text",
                        },
                    },
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Cancelar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value)
                        {
                            axios
                                .patch(URI+ payload.id+'/cancelar',  {id:payload.id, motivo: value}, { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Factura cancelada correctamente", {
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
                        else if(value == '')
                        {
                            swal("Ingrese el motivo de cancelación de la factura.",{icon: "error"});
                        }
                    });
            });
        },
        store(context, payload){
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar la Factura",
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
                }).then((value) => {
                    if (value) {
                        axios
                            .post(URI, payload)
                            .then(r => r.data)
                            .then(data => {
                                swal("Factura registrada correctamente", {
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
        },
        cargarCFDI(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'CFDI', payload.data, payload.config)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
    },

    getters: {
        facturas(state) {
            return state.facturas;
        },
        meta(state) {
            return state.meta;
        },
        currentFactura(state) {
            return state.currentFactura;
        },
    }
}
