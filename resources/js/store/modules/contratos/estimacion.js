const URI = '/api/contratos/estimacion/';

export default {
    namespaced: true,
    state: {
        estimaciones: [],
        currentEstimacion: null,
        meta: {},
        amortizacion: null

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

        UPDATE_AMORTIZACION(state, data){
            state.amortizacion = data;
        },

        UPDATE_ATTRIBUTE(state, data) {
            _.set(state.currentEstimacion, data.attribute, data.value);
        },

        UPDATE_CUENTA(state, data) {
            state.estimaciones = state.estimaciones.map(estimacion => {
                if (estimacion.id === data.id) {
                    return Object.assign({}, estimacion, data)
                }
                return estimacion
            })
            state.currentEstimacion = data;
        },

        APROBAR_ESTIMACION(state, id) {
            state.estimaciones.forEach(estimacion => {
                if(estimacion.id == id) {
                    estimacion.estado = 1;
                }
            })
        },

        REVERTIR_APROBACION(state, id) {
            state.estimaciones.forEach(estimacion => {
                if(estimacion.id == id) {
                    estimacion.estado = 0;
                }
            })
        },
        DELETE_ENTRADA(state, id) {
            state.estimaciones = state.estimaciones.filter(estimacion => {
                return estimacion.id != id
            });
        }
    },

    actions: {
        pdf(context, payload) {
            axios.get(URI + payload+'/formato-orden-pago', {params: payload.params})
                .then((data) => {

                });
        },
        amortizacion(context, payload) {
            return new Promise((resolve, reject) => {

                swal({
                    title: "¿Está seguro?",
                    text: "Actualizar Amortización de Anticipo",
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
                                .patch(URI + payload.id + '/amortizacion', payload.data,{ params: payload.params } )
                                .then(r => r.data)
                                .then(data => {
                                    swal("Amortizacion de Anticipo actualizado correctamente", {
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
        update(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Actualizar la Estimación",
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
                                    swal("Estimación actualizada correctamente", {
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
        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Estimación",
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
                                    swal("Estimación registrada correctamente", {
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
        find (context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id, { params: payload.params })
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        ordenarConceptos (context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id+'/ordenarConceptos', { params: payload.params })
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        paginate (context, payload){
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
            });
        },
        aprobar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Aprobar Estimación",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Aprobar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .patch(URI + payload.id + '/aprobar')
                                .then(r => r.data)
                                .then(data => {
                                    swal("Estimación aprobada correctamente", {
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
                        } else {
                            reject();
                        }
                    });
            });
        },
        revertirAprobacion(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Revertir Aprobación",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Revertir',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .patch(URI + payload.id + '/revertirAprobacion')
                                .then(r => r.data)
                                .then(data => {
                                    swal("Aprobación revertida correctamente", {
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
                        } else {
                            reject();
                        }
                    });
            });
        },
        eliminar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Eliminar la Estimación",
                    text: "¿Está seguro de que desea eliminar esta estimación?",
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
                                .delete(URI + payload.id, {params: payload.params})
                                .then(r => r.data)
                                .then(data => {
                                    swal("Estimación eliminada correctamente", {
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
        registrarRetencionISR(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Retención ISR",
                    text: "¿Está seguro de que desea registrar esta retención de ISR?",
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
                                .patch(URI + payload.id + '/registrarRetencionIsr', payload.params)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Retención ISR registrada correctamente", {
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
            var urr = URI + 'descargaLayout/'+ payload.id +'?db=' + this._vm.$session.get('db') + '&idobra=' + this._vm.$session.get('id_obra') + '&access_token=' + this._vm.$session.get('jwt');
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
                    title: "Cargar Layout de Estimación",
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
        cargaLayoutEdit(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Cargar Layout de Estimación",
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
        descargaLayoutEdicion(context, payload){
            var urr = URI + 'descargaLayoutEdicion/'+ payload.id +'?db=' + this._vm.$session.get('db') + '&idobra=' + this._vm.$session.get('id_obra') + '&access_token=' + this._vm.$session.get('jwt');
            var win = window.open(urr, "_blank");

            win.onbeforeunload = () => {
                swal("Layout descargado correctamente.", {
                    icon: "success",
                    timer: 2000,
                    buttons: false
                })
            }
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
