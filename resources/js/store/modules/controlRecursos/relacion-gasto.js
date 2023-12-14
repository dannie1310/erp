const URI = '/api/control-recursos/relacion-gasto/';

export default {
    namespaced: true,
    state: {
        relaciones: [],
        currentRelacion: null,
        meta: {}
    },

    mutations: {
        SET_RELACIONES(state, data) {
            state.relaciones = data
        },
        SET_RELACION(state, data)
        {
            state.currentRelacion = data;
        },
        SET_META(state, data) {
            state.meta = data;
        },
        UPDATE_RELACION(state, data) {
            state.relaciones = state.relaciones.map(relacion => {
                if (relacion.id === data.id) {
                    return Object.assign([], relacion, data)
                }
                return relacion
            })
            state.currentRelacion = data
        },

        UPDATE_ATTRIBUTE(state, data) {
            state.currentRelacion[data.attribute] = data.value
        },
        DELETE_RELACION(state, id) {
            state.relaciones = state.relaciones.filter(d => {
                return d.id != id
            });
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
        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Relación de Gasto",
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
                                    swal("Relación de Gasto registrado correctamente", {
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
        update(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Actualizar la Relación de Gastos",
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
                                    swal("Relación de gastos actualizado correctamente", {
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
        close(context, payload) {

            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Cerrar la Relación de Gastos",
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
                                    swal("Relación cerrada correctamente", {
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
                    text: "Abrir la Relación de Gastos",
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
                                    swal("Relación abierta correctamente", {
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
                    title: "Eliminar Relación de Gastos",
                    text: "¿Está seguro de que desea eliminar el relación?",
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
                                    swal("Relación de gastos eliminado correctamente", {
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
    },

    getters: {
        relaciones(state) {
            return state.relaciones
        },
        meta(state) {
            return state.meta
        },
        currentRelacion(state) {
            return state.currentRelacion;
        }
    }
}
