const URI = '/api/almacen/';

export default {
    namespaced: true,
    state: {
        almacenes: [],
        currentAlmacen: '',
        meta: {}
    },

    mutations: {
        SET_ALMACENES(state, data) {
            state.almacenes = data;
        },
        SET_ALMACEN(state, data) {
            state.currentAlmacen = data;
        },
        SET_META(state, data) {
            state.meta = data;
        },
        UPDATE_ALMACEN(state, data) {
            state.almacenes = state.almacenes.map(almacen => {
                if (almacen.id === data.id) {
                    return Object.assign({}, almacen, data)
                }
                return almacen
            })
            state.currentAlmacen = state.currentAlmacen ? data : null;
        },
        UPDATE_ATTRIBUTE(state, data) {
            state.currentAlmacen[data.attribute] = data.value
        }
    },

    actions: {
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
        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar",
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
                                    swal("Almacén registrado correctamente", {
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
                    title: "Actualizar el Almacén",
                    text: "¿Está seguro/a de que desea actualizar el almacén?",
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
                                swal("El Almacén ha sido actualizado correctamente", {
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
        almacenes(state) {
            return state.almacenes
        },
        currentAlmacen(state) {
            return state.currentAlmacen
        },
        meta(state) {
            return state.meta;
        },
    }
}
