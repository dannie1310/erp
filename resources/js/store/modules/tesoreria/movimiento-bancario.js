const URI = '/api/tesoreria/movimiento-bancario/';

export default {
    namespaced: true,
    state: {
        movimientos: [],
        currentMovimiento: null,
        meta: {},
    },

    mutations: {
        SET_MOVIMIENTOS(state, data) {
            state.movimientos = data
        },

        SET_MOVIMIENTO(state, data) {
            state.currentMovimiento = data
        },

        SET_META(state, data) {
            state.meta = data
        },


        DELETE_MOVIMIENTO(state, id) {
            state.movimientos = state.movimientos.filter((mov) => {
                return mov.id !== id;
            })
            if (state.currentMovimiento && state.currentMovimiento.id === id) {
                state.currentMovimiento = null;
            }
        },

        UPDATE_ATTRIBUTE(state, data) {
            _.set(state.currentMovimiento, data.attribute, data.value);
        },

        UPDATE_MOVIMIENTO(state, data) {
            state.movimientos = state.movimientos.map(movimiento => {
                if (movimiento.id === data.id) {
                    return Object.assign({}, movimiento, data)
                }
                return movimiento
            })
            state.currentMovimiento = state.currentMovimiento ? data : null;
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
                    title: "Eliminar movimiento",
                    text: "¿Estás seguro/a de que deseas eliminar este movimiento?",
                    icon: "warning",
                    buttons: ['Cancelar',
                        {
                            text: "Si, Eliminar",
                            closeModal: false,
                        }
                    ],
                    dangerMode: true,
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .delete(URI + id)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Movimiento eliminado correctamente", {
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
                    title: "Registrar movimiento",
                    text: "¿Estás seguro/a de que la información es correcta?",
                    icon: "info",
                    buttons: ['Cancelar',
                        {
                            text: "Si, Registrar",
                            closeModal: false,
                        }
                    ]
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI, payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Movimiento registrado correctamente", {
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
                    text: "Guardar cambios del Movimiento",
                    icon: "warning",
                    buttons: ['Cancelar',
                        {
                            text: "Si, Guardar",
                            closeModal: false,
                        }
                    ]
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .patch(URI + payload.id, payload.data, { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Movimiento Actualizado correctamente", {
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
        movimientos(state) {
            return state.movimientos
        },

        meta(state) {
            return state.meta
        },

        currentMovimiento(state) {
            return state.currentMovimiento
        }
    }
}