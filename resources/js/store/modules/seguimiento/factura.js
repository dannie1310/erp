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
                    buttons: [
                        'Cancelar',
                        {
                            text: "Si, Cancelar",
                            closeModal: false,
                        }
                    ]
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .patch(URI+ payload.id+'/cancelar',  {id:payload.id, motivo: value}, { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal({
                                        title: "Cancelación exitosa",
                                        text: " ",
                                        icon: "success",
                                        timer: 3000,
                                        buttons: false
                                    }).then(() => {
                                        resolve(data);

                                    })
                                })
                                .catch(error => {
                                    reject(error);
                                });
                        } else {
                            swal("Ingrese el motivo de cancelación de la factura.",{icon: "error"});
                        }
                    });
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
