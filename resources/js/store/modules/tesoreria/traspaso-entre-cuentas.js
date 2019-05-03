const URI = '/api/tesoreria/traspaso-entre-cuentas/';

export default {
    namespaced: true,
    state: {
        traspasos: [],
        currentTraspaso: null,
        meta: {},
    },

    mutations: {
        SET_TRASPASOS(state, data) {
            state.traspasos = data
        },

        SET_TRASPASO(state, data) {
            state.currentTraspaso = data
        },

        SET_META(state, data) {
            state.meta = data
        },


        DELETE_TRASPASO(state, id) {
            state.traspasos = state.traspasos.filter((t) => {
                return t.id_traspaso !== id;
            })
            if (state.currentTraspaso && state.currentTraspaso.id === id) {
                state.currentTraspaso = null;
            }
        },

        UPDATE_ATTRIBUTE(state, data) {
            _.set(state.currentTraspaso, data.attribute, data.value);
        },

        UPDATE_TRASPASO(state, data) {
            state.traspasos = state.traspasos.map(traspaso => {
                if (traspaso.id_traspaso === data.id_traspaso) {
                    return Object.assign({}, traspaso, data)
                }
                return traspaso
            })
            state.currentTraspaso = state.currentTraspaso ? data : null;
        }
    },

    actions: {
        paginate (context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'paginate', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data)
                    })
                    .catch(error => {
                        reject(error)
                    })
            })
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

        delete(context, id) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Eliminar Traspaso",
                    text: "¿Estás seguro/a de que deseas eliminar este Traspaso?",
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
                                .delete(URI + id)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Traspaso eliminado correctamente", {
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

        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar traspaso",
                    text: "¿Estás seguro/a de que la información es correcta?",
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
                                    swal("Traspaso registrado correctamente", {
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
                    });
            });
        },

        update(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Estás seguro?",
                    text: "Guardar cambios del Traspaso",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Guardar',
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
                                    swal("Traspaso Actualizado correctamente", {
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
        traspasos(state) {
            return state.traspasos
        },

        meta(state) {
            return state.meta
        },

        currentTraspaso(state) {
            return state.currentTraspaso
        }
    }
}