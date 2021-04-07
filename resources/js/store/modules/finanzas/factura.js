const URI = '/api/finanzas/factura/';

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
        UPDATE_FACTURA(state, data) {
            state.facturas = state.facturas.map(factura => {
                if (factura.id === data.id) {
                    return Object.assign([], factura, data)
                }
                return factura
            })
            state.currentFactura = data
        },

        UPDATE_ATTRIBUTE(state, data) {
            state.currentFactura[data.attribute] = data.value
        },

        SET_ITEMS_REVISION(state, data){
            state.items_revision = data;
        },
        UPDATE_ITEM_PENDIENTE(state, data) {
            // state.items_revision.pendientes.forEach(function (item){
            //     item.seleccionado = data.seleccionado;
            //     console.log(data);
            // });
            state.items_revision.pendientes = state.items_revision.pendientes.map(factura => {
                if (factura.id_item === data.id_item) {
                    return Object.assign([], factura, data)
                }
                return factura
            })
            state.currentFactura = data
        },
    },

    actions: {
        delete(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Eliminar Factura",
                    text: "¿Está seguro/a de que desea eliminar factura?",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Eliminar',
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
                                    swal("Factura eliminada correctamente", {
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
        index(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    });
            });
        },
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
        getDocumentos(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id + '/getDocumentos', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },
        revertir(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Revertir Factura",
                    text: "¿Está seguro de revertir la factura?",
                    icon: "info",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Revertir',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .get(URI + payload.id + "/revertir", { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Revisión de factura revertida correctamente", {
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
        cargarXML(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'xml', payload.data, payload.config)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Factura",
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
        storeRevision(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Revisión Factura",
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
                                .post(URI + 'storeRevision', payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Revisión de Factura registrada correctamente", {
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
        storeRevisionVarios(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Revisión Factura de Varios",
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
                                .post(URI + 'storeRevisionVarios', payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Revisión de Factura de varios registrada correctamente", {
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
        autorizadas(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'autorizada', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    });
            });
        },
        pendientes_pago(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI +payload.id + '/pendientesPago', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    });
            });
        },
        update(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Actualizar la Factura",
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
                                .patch(URI + payload.id, payload.data,{ params: payload.params } )
                                .then(r => r.data)
                                .then(data => {
                                    swal("Factura actualizada correctamente", {
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
        facturas(state) {
            return state.facturas;
        },

        meta(state) {
            return state.meta;
        },

        currentFactura(state) {
            return state.currentFactura;
        },

        items_revision(state){
            return state.items_revision;
        },
    }
}
