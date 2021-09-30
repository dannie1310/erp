const URI = '/api/contratos/presupuesto/';

export default {
    namespaced: true,
    state: {
        presupuestos: [],
        currentPresupuesto: null,
        meta: {}
    },

    mutations: {
        SET_PRESUPUESTOS(state, data) {
            state.presupuestos = data
        },

        SET_META(state, data) {
            state.meta = data;
        },

        SET_PRESUPUESTO(state, data) {
            state.currentPresupuesto = data;
        }
    },

    actions: {
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
        delete(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Eliminar Presupuesto Contratista",
                    text: "¿Está seguro/a de que desea eliminar presupuesto?",
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
                                .delete(URI + payload.id, { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Presupuesto eliminado correctamente", {
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
        store(context,payload){
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Presupuesto Contratista",
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
                    }                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI, payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Presupuesto registrado correctamente", {
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
        update(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Estás seguro?",
                    text: "Actualizar Presupuesto Contratista",
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
                                .patch(URI + payload.id, payload.post)
                                .then(r => r.data)
                                .then(data => {
                                    swal("El Presupuesto Contratista se ha actualizado correctamente", {
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
        cargaLayout(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Cargar Layout de Presupuesto Contratista",
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
                                        swal("Archivo cargado correctamente:", {
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
        registrarPresupuestoProveedor(context,payload){
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Presupuesto Contratista",
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
                    }                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI+"portal-proveedor", payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Presupuesto registrado correctamente", {
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
        updatePresupuestoProveedor(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Estás seguro?",
                    text: "Actualizar Presupuesto Contratista",
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
                                .patch(URI + payload.id + "/portal-proveedor", payload.presupuesto)
                                .then(r => r.data)
                                .then(data => {
                                    swal("El Presupuesto Contratista se ha actualizado correctamente", {
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
        descargaLayoutProveedor(context, payload){
            var urr = URI + 'descargaLayoutProveedor/'+ payload.id +'?id_presupuesto=' + payload.id_presupuesto+ '&idobra=' + '&access_token=' + this._vm.$session.get('jwt');
            var win = window.open(urr, "_blank");

            win.onbeforeunload = () => {
                swal("Layout descargado correctamente.", {
                    icon: "success",
                    timer: 2000,
                    buttons: false
                })
            }
        },
        cargaLayoutProveedor(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Cargar Layout de Presupuesto Contratista",
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
                                .post(URI + 'layoutProveedor', payload.data, payload.config)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Archivo cargado correctamente:", {
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
        deletePresupuestoProveedor(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Eliminar Presupuesto Contratista",
                    text: "¿Está seguro/a de que desea eliminar presupuesto?",
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
                                .delete(URI + payload.id + "/proveedor", { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Presupuesto eliminado correctamente", {
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
        enviarPresupuesto(context, payload) {
            return new Promise((resolve, reject) => {
                if (payload.cotizacion_completa == false) {
                    swal({
                        title: "Partidas faltantes de cotizar",
                        text: "¿Está seguro de enviar el presupuesto con partidas faltantes de cotizar?",
                        icon: "warning",
                        closeOnClickOutside: false,
                        buttons: {
                            cancel: {
                                text: 'Cancelar',
                                visible: true
                            },
                            confirm: {
                                text: 'Si, Enviar',
                                closeModal: false,
                            }
                        }
                    }).then((value) => {
                        if (value) {
                            axios
                                .patch(URI + payload.id + '/portal-proveedor/enviar', payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("El presupuesto se ha enviado correctamente", {
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
                }else {
                    swal({
                        title: "¿Estás seguro?",
                        text: "Enviar Presupuesto",
                        icon: "warning",
                        buttons: {
                            cancel: {
                                text: 'Cancelar',
                                visible: true
                            },
                            confirm: {
                                text: 'Si, Enviar',
                                closeModal: false,
                            }
                        }
                    })
                    .then((value) => {
                        if (value) {
                            axios
                                .patch(URI + payload.id + '/portal-proveedor/enviar', payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("El presupuesto se ha enviado correctamente", {
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
                }
            });
        },
    },

    getters: {
        presupuestos(state) {
            return state.presupuestos
        },

        meta(state) {
            return state.meta
        },

        currentPresupuesto(state) {
            return state.currentPresupuesto;
        }
    }
}
