const URI = '/api/portal-proveedor/solicitud-autorizacion-avance/';

export default {
    namespaced: true,
    state: {
        estimaciones: [],
        currentEstimacion: null,
        meta: {},
    },

    mutations: {
        SET_ESTIMACIONES(state, data) {
            state.estimaciones = data
        },

        SET_ESTIMACION(state, data) {
            state.currentEstimacion = data;
        },

        SET_META(state, data) {
            state.meta = data
        },

        UPDATE_ATTRIBUTE(state, data) {
            _.set(state.currentEstimacion, data.attribute, data.value);
        },
    },

    actions: {
        index(context, payload){
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'index', { params: payload.params })
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
                    title: "Registrar Solicitud de Autorización",
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
                                    swal("Solicitud de autorización registrada correctamente", {
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
        ordenarConceptos (context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + payload.id+'/ordenarConceptos', payload)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        update(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Actualizar la Solicitud de Autorización",
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
                                    swal("Solicitud de autorización actualizada correctamente", {
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
        eliminar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Eliminar la Solicitud de Autorización",
                    text: "¿Está seguro de que desea eliminar esta solicitud?",
                    icon: "warning",
                    closeOnClickOutside: false,
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
                            .patch(URI + payload.id+'/eliminar',payload)
                            .then(r => r.data)
                            .then(data => {
                                swal("Solicitud de Autorización eliminada correctamente", {
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
                    } else {
                        reject();
                    }
                });
            });
        },
        registrarRetencionIva(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Retención IVA",
                    text: "¿Está seguro de que desea registrar esta retención de IVA?",
                    icon: "warning",
                    closeOnClickOutside: false,
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
                                .patch(URI + payload.id + '/registrarRetencionIva', payload.params)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Retención IVA registrada correctamente", {
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
                        } else {
                            reject();
                        }
                    });
            });
        },
        descargaLayout(context, payload){
            var urr = URI + 'descargaLayout/'+ payload.id +'?db=' + payload.base + '&access_token=' + this._vm.$session.get('jwt');
            var win = window.open(urr, "_blank");

            win.onbeforeunload = () => {
                swal("Layout descargado correctamente.", {
                    icon: "success",
                    timer: 2000,
                    buttons: false
                })
            }
        },
        cargaLayout(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Cargar Layout de Solicitud",
                    text: "¿Está seguro/a de que desea cargar xlsx?",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Agregar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI + 'layout', payload.data, payload.config)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Archivo leido correctamente", {
                                        icon: "success",
                                        timer: 2000,
                                        buttons: false
                                    }).then(() => {
                                        resolve(data);
                                    })

                                })
                                .catch(error => {
                                    reject('Archivo no procesable');
                                })
                        }
                    });
            });
        },
        descargaEdicionLayout(context, payload){
            var urr = URI + 'descargaLayoutEdicion/'+ payload.id +'?db=' + payload.base + '&access_token=' + this._vm.$session.get('jwt');
            var win = window.open(urr, "_blank");

            win.onbeforeunload = () => {
                swal("Layout descargado correctamente.", {
                    icon: "success",
                    timer: 2000,
                    buttons: false
                })
            }
        },
        cargaLayoutEdit(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Cargar Layout de Solicitud",
                    text: "¿Está seguro/a de que desea cargar xlsx?",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Agregar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI + 'layoutEdit', payload.data, payload.config)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Archivo leido correctamente", {
                                        icon: "success",
                                        timer: 2000,
                                        buttons: false
                                    }).then(() => {
                                        resolve(data);
                                    })

                                })
                                .catch(error => {
                                    reject('Archivo no procesable');
                                })
                        }
                    });
            });
        },
    },
    getters: {
        estimaciones(state) {
            return state.estimaciones
        },

        meta(state) {
            return state.meta
        },

        currentEstimacion(state) {
            return state.currentEstimacion;
        }
    }
}
