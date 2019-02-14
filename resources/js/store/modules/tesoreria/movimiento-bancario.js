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
            state.currentMovimiento = data;
        }
    },

    actions: {
        paginate (context, payload){
            context.commit('SET_MOVIMIENTOS', [])
            axios
                .get(URI + 'paginate', { params: payload })
                .then(r => r.data)
                .then(data => {
                    context.commit('SET_MOVIMIENTOS', data.data)
                    context.commit('SET_META', data.meta)
                })
        },

        find(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id, { params: payload.params })
                    .then(r => r.data)
                    .then((data) => {
                        context.commit('SET_MOVIMIENTO', data)
                        resolve();
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
                    buttons: ['Cancelar', 'Si, Eliminar'],
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
                                        context.commit('DELETE_MOVIMIENTO', id);
                                        resolve();
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
                    buttons: ['Cancelar', 'Si, Registrar']
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
                    buttons: ['Cancelar', 'Si, Guardar']
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .patch(URI + payload.id, payload.data, { params: payload.params })
                                .then(r => r.data)
                                .then((data) => {
                                    swal("Movimiento Actualizado correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    })
                                        .then(() => {
                                            context.commit('UPDATE_MOVIMIENTO', data);
                                            resolve();
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