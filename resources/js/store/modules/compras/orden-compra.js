const URI = '/api/compras/orden-compra/';

export default {
    namespaced: true,
    state: {
        ordenes: [],
        currentOrden: null,
        meta: {}
    },

    mutations: {
        SET_ORDENES(state, data) {
            state.ordenes = data
        },
        SET_ORDEN(state, data) {
            state.currentOrden = data;
        },
        SET_META(state, data) {
            state.meta = data
        },
    },

    actions: {
        index(context, payload = {}) {
            return new Promise((resolve, reject) => {

                axios
                    .get(URI, payload.config)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data.data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        paginate (context, payload){
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'paginate', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },

        find (context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI+payload.id, {params: payload.params})
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        eliminarOrdenes(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Eliminar Orden(es) de Compra",
                    text: "¿Estás seguro/a de que desea eliminar la(s) orden(es) de Compra?",
                    icon: "warning",
                    closeOnClickOutside: false,
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Eliminar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI + 'eliminarOrdenes', payload.data)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Requisición de Compra eliminada correctamente", {
                                        icon: "success",
                                        timer: 1500,
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
        update(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Actualizar Orden de COmpra",
                    text: "¿Está seguro/a de que desea actualizar la orden de compra?",
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
                                .patch(URI + payload.id, payload.data, { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("La orden de compra ha sido actualizada correctamente", {
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
    },

    getters: {
        ordenes(state) {
            return state.ordenes
        },
        currentOrden(state) {
            return state.currentOrden
        },
        meta(state) {
            return state.meta
        },
    }
}