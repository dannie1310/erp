const URI = '/api/contabilidad-general/cuenta/';
export default {
    namespaced: true,
    state: {
        cuentas: [],
        currentCuenta: null,
        meta: {},
    },

    mutations: {
        SET_CUENTAS(state, data) {
            state.cuentas = data;
        },

        SET_META(state, data) {
            state.meta = data;
        },

        SET_CUENTA(state, data) {
            state.currentCuenta = data;
        },

        UPDATE_CUENTA(state, data) {
            state.cuentas = state.cuentas.map(e => {
                if (e.id === data.id) {
                    return data
                }
                return e
            })
            state.currentCuenta = data;
        },
    },

    actions: {
        index(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI, {params: payload.params})
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    });
            });
        },
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
        asociarProveedor(context, payload){
            return new Promise((resolve, reject) => {
                swal({
                    title: "Asociar Masivamente Cuentas con Proveedor",
                    text: "¿Está seguro?",
                    icon: "info",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Asociar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI + 'asociar-proveedor', payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal(data.mensaje, {
                                        icon: data.icon,
                                        timer: 7000,
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
        asociarCuenta(context, payload){
            return new Promise((resolve, reject) => {
                swal({
                    title: "Asociar Cuenta con Proveedor",
                    text: "¿Está seguro de que la información es correcta?",
                    icon: "info",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Asociar',
                            closeModal: false,
                        }
                    }
                })
                .then((value) => {
                    if (value) {
                        axios
                            .post(URI + 'asociar', payload.params)
                            .then(r => r.data)
                            .then(data => {
                                swal("Asociación registrada correctamente", {
                                    icon: "success",
                                    timer: 3000,
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
        eliminarAsociacion(context, payload){
            return new Promise((resolve, reject) => {
                swal({
                    title: "Eliminar Asociación Cuenta con Proveedor",
                    text: "¿Está seguro de que desea eliminar la asociación?",
                    icon: "info",
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
                            .post(URI + 'eliminar-asociacion', payload)
                            .then(r => r.data)
                            .then(data => {
                                swal("Asociación eliminada correctamente", {
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
