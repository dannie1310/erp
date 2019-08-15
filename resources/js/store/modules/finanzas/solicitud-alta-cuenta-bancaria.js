const URI = '/api/finanzas/gestion-cuenta-bancaria/solicitud-alta/';

export default{
    namespaced: true,
    state: {
        cuentas: [],
        currentCuenta: null,
        meta: {}
    },

    mutations: {
        SET_CUENTAS(state, data){
            state.cuentas = data
        },

        SET_META(state, data){
            state.meta = data
        },

        SET_CUENTA(state, data){
            state.currentCuenta = data
        },

        UPDATE_ATTRIBUTE(state, data) {
            _.set(state.currentCuenta, data.attribute, data.value);
        },

        UPDATE_CUENTA(state, data) {
            state.cuentas = state.cuentas.map(cuentas => {
                if (cuentas.id === data.id) {
                    return Object.assign({}, cuentas, data)
                }
                return cuentas
            })
            state.currentCuenta = data;
        },
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
                        reject(error);
                    })
            });
        },
        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Solicitud de Alta de Cuenta Bancaria",
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
                                    swal("Solicitud de alta registrada correctamente", {
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

        autorizar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Solicitud de Alta de Cuenta Bancaria",
                    text: "¿Estás seguro/a de autorizar la solicitud de alta de cuenta bancaria?",
                    icon: "info",
                    closeOnClickOutside: false,
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Autorizar',
                            closeModal: false,
                        }
                    }
                }) .then((value) => {
                    if (value) {
                        axios
                            .get(URI + payload.id + '/autorizar', {params: payload.params})
                            .then(r => r.data)
                            .then(data => {
                                swal("La autorizacion ha sido aplicada exitosamente", {
                                    icon: "success",
                                    timer: 2000,
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

        rechazar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Solicitud de Alta de Cuenta Bancaria",
                    text: "¿Estás seguro/a de rechazar la solicitud de alta de cuenta bancaria?",
                    icon: "warning",
                    closeOnClickOutside: false,
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Rechazar',
                            closeModal: false,
                        }
                    }
                }) .then((value) => {
                    if (value) {
                        axios
                            .get(URI + payload.id + '/rechazar', {params: payload.params})
                            .then(r => r.data)
                            .then(data => {
                                swal("El rechazo ha sido aplicado exitosamente", {
                                    icon: "success",
                                    timer: 2000,
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