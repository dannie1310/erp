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
        DELETE_ORDEN(state, id){
            state.ordenes = state.ordenes.filter(orden => {
                return orden.id != id
            });
        }
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
        eliminar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Eliminar Orden de Compra",
                    text: "¿Está seguro de que desea eliminar la Orden de Compra?",
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
                                .delete(URI + payload.id, { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Orden de Compra eliminada correctamente", {
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
        eliminarOrdenes(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Eliminar Orden(es) de Compra",
                    text: "¿Está seguro/a de que desea eliminar la(s) Orden(es) de Compra?",
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
                                    swal("Orden de Compra eliminada correctamente", {
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
                    title: "Actualizar Orden de Compra",
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
