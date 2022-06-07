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
                    title: "Factura",
                    text: "¿Está seguro de cancelar la factura?",
                    icon: "warning",
                    closeOnClickOutside: false,
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Continuar',
                            closeModal: false,
                        }
                    }
                }) .then((value) => {
                    if (value) {
                        axios
                            .get(URI + payload.id + '/cancelar', {params: payload.params})
                            .then(r => r.data)
                            .then(data => {
                                swal("La cancelación ha sido aplicada exitosamente", {
                                    icon: "success",
                                    timer: 2000,
                                    buttons: false
                                }).then(() => {
                                    resolve(data);
                                })
                            })
                            .catch(error =>  {
                                reject(error);
                            });
                    } else {
                        reject();
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
