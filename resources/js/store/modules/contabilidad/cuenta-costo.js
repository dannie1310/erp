const URI = '/api/contabilidad/cuenta-costo/';

export default {
    namespaced: true,
    state: {
        cuentas: [],
        currentCuenta: null,
        meta: {}
    },

    mutations: {
        SET_CUENTAS(state, data) {
            state.cuentas = data
        },

        SET_META(state, data) {
            state.meta = data
        },

        SET_CUENTA(state, data) {
            state.currentCuenta = data
        },

        UPDATE_CUENTA(state, data) {
            state.cuentas = state.cuentas.map(cuenta => {
                if (cuenta.id === data.id) {
                    return Object.assign([], cuenta, data)
                }
                return cuenta
            })
            if (state.currentCuenta) {
                state.currentCuenta = data
            }
        },

        UPDATE_ATTRIBUTE(state, data) {
            state.currentCuenta[data.attribute] = data.value
        }
    },

    actions: {
        find(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
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
                    title: "Registrar Cuenta",
                    text: "¿Estás seguro/a de que la información es correcta?",
                    icon: "info",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
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
                                    swal("Cuenta registrada correctamente", {
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
                    text: "Actualizar Cuenta de Material",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
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
                                .patch(URI + payload.id, payload.data)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Cuenta actualizada correctamente", {
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
        }
    },

    getters: {
        cuentas(state) {
            return state.cuentas
        },

        meta(state) {
            return state.meta
        },

        currentCuenta(state) {
            return state.currentCuenta
        }
    }
}