const URI = '/api/acarreos/tiro/';

export default {
    namespaced: true,
    state: {
        tiros: [],
        currentTiro: '',
        meta:{}
    },

    mutations: {
        SET_TIROS(state, data) {
            state.tiros = data;
        },
        SET_TIRO(state, data) {
            state.currentTiro = data;
        },
        SET_META(state, data) {
            state.meta = data;
        },

        UPDATE_ATTRIBUTE(state, data) {
            _.set(state.currentTiro, data.attribute, data.value);
        },

        UPDATE_TIRO(state, data) {
            state.tiros = state.tiros.map(tiro => {
                if (tiro.id === data.id) {
                    return Object.assign({}, tiro, data)
                }
                return tiro
            })
            state.currentTiro = data;
        },
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
                    })
            })
        },
        agregarConcepto(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: payload.params.data.concepto == 1 ? "Sustituir el concepto" : "Asignar el concepto",
                    text: payload.params.data.concepto == 1 ?  "¿Está seguro de que desea sustituir el concepto del tiro?" : "¿Está seguro de que desea asignar el concepto al tiro?",
                    icon: "warning",
                    closeOnClickOutside: false,
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: payload.params.data.concepto == 1 ?  'Si, Sustituir' : 'Si, Asignar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .get(URI+ payload.id+'/asignar', { params: payload.params.data.id_concepto })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Concepto asignado correctamente", {
                                        icon: "success",
                                        timer: 1500,
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
        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Tiro",
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
                                    swal("Tiro registrado correctamente", {
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
        activar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Activar el Tiro",
                    text: "¿Está seguro de que deseas activar el tiro?",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Activar',
                            closeModal: false,
                        }
                    },
                    dangerMode: true,
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .get(URI + payload.id+'/activar', { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Tiro activado correctamente", {
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
        desactivar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Desactivar el Tiro",
                    text: "¿Está seguro de que deseas desactivar el tiro?",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Desactivar',
                            closeModal: false,
                        }
                    },
                    dangerMode: true,
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .get(URI + payload.id+'/desactivar', { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Tiro desactivado correctamente", {
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
        tiros(state) {
            return state.tiros
        },
        currentTiro(state) {
            return state.currentTiro
        },
        meta(state) {
            return state.meta;
        },
    }
}
