const URI = '/api/almacenes/conteo/';
export default{
    namespaced: true,
    state: {
        conteos: [],
        currentConteo: null,
        meta: {}
    },

    mutations: {
        SET_CONTEOS(state, data){
            state.conteos = data
        },

        SET_META(state, data){
            state.meta = data
        },

        SET_CONTEO(state, data){
            state.currentConteo = data
        },

        UPDATE_CONTEO(state, data) {
            state.conteos = state.conteos.map(conteo => {
                if (conteo.id === data.id) {
                    return Object.assign({}, conteo, data)
                }
                return conteo
            })
            state.currentConteo = data;
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

        cancelar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Cancelar Conteo",
                    text: "¿Está seguro que quiere cancelar el conteo?",
                    icon: "warning",
                    closeOnClickOutside: false,
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Continuar',
                            closeModal: false,
                        }
                    }
                }) .then((value) => {
                    if (value) {
                        axios
                            .get(URI + payload.id + '/cancelar', {params: payload.params})
                            .then(r => r.data)
                            .then(data => {
                                swal("La cancelación ha sido aplicada exitosamente", {
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

        cargaManualLayout(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Cargar Layout manual de conteo",
                    text: "¿Está seguro de que desea generar conteo?",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Generar',
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
                                    if(data.length > 0){
                                        swal("No se pudieron insertar los siguientes conteos:"+data, {
                                            buttons: {
                                                confirm: {
                                                    text: 'Aceptar',
                                                    closeModal: true,
                                                }
                                            }
                                        }).then(() => {
                                            resolve(data);
                                        })
                                    }else{
                                        swal("Conteos registrados correctamente:"+data, {
                                            icon: "success",
                                            timer: 2000,
                                            buttons: false
                                        }).then(() => {
                                            resolve(data);
                                        })
                                    }

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
                        reject(error);
                    })
            });
        },
        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar conteo manual",
                    text: "¿Está seguro de que quiere registrar un conteo manual?",
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
                                    swal("Conteo registrado correctamente", {
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
        storeCodigoBarra(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar conteo manual",
                    text: "¿Está seguro de que quiere registrar un conteo por código de barra?",
                    icon: "info",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true,
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
                                .post(URI+'codigo-barra', payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Conteo registrado correctamente", {
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
        conteos(state) {
            return state.conteos
        },

        meta(state) {
            return state.meta
        },

        currentConteo(state) {
            return state.currentConteo
        }
    }
}