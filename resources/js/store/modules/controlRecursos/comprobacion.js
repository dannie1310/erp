const URI = '/api/control-recursos/comprobacion/';

export default {
    namespaced: true,
    state: {
        comprobaciones: [],
        currentComprobacion: null,
        meta: {}
    },

    mutations: {
        SET_COMPROBACIONES(state, data) {
            state.comprobaciones = data
        },
        SET_COMPROBACION(state, data)
        {
            state.currentComprobacion = data;
        },
        SET_META(state, data) {
            state.meta = data;
        },
        UPDATE_COMPROBACION(state, data) {
            state.comprobaciones = state.comprobaciones.map(c => {
                if (c.id === data.id) {
                    return Object.assign([], c, data)
                }
                return c
            })
            state.currentComprobacion = data
        },

        UPDATE_ATTRIBUTE(state, data) {
            state.currentComprobacion[data.attribute] = data.value
        },
        DELETE_COMPROBACION(state, id) {
            state.comprobaciones = state.comprobaciones.filter(d => {
                return d.id != id
            });
        }
    },

    actions: {
        paginate(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'paginate', {params: payload.params})
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
                    .get(URI + payload.id, {params: payload.params})
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
                    title: "Registrar la Comprobación",
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
                                    swal("Comprobación registrada correctamente", {
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
        update(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Actualizar la Comprobación de Gastos",
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
                }).then((value) => {
                    if (value) {
                        axios
                            .patch(URI + payload.id, payload.data,{ params: payload.params } )
                            .then(r => r.data)
                            .then(data => {
                                swal("Comprobación de gastos actualizado correctamente", {
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
        delete(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Eliminar Comprobación de Gastos",
                    text: "¿Está seguro de que desea eliminar el comprobación?",
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
                                    swal("Comprobación de gastos eliminado correctamente", {
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
        close(context, payload) {

            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Cerrar la Comprobación de Gastos",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Cerrar',
                            closeModal: false,
                        }
                    },
                    dangerMode: true
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .get(URI + payload.id + '/close', { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Comprobación cerrada correctamente", {
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
        open(context, payload) {

            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Abrir la Comprobación de Gastos",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Abrir',
                            closeModal: false,
                        }
                    },
                    dangerMode: true
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .get(URI + payload.id + '/open', { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Comprobación abierta correctamente", {
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
        comprobaciones(state) {
            return state.comprobaciones
        },
        meta(state) {
            return state.meta
        },
        currentComprobacion(state) {
            return state.currentComprobacion;
        }
    }
}
