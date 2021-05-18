const URI = '/api/SEGURIDAD_ERP/efo/';


export default {
    namespaced: true,
    state: {
        efos: [],
        currentEfo: null,
        meta: {}
    },

    mutations: {
        SET_EFOS(state, data) {
            state.efos = data
        },

        SET_EFO(state, data) {
            state.currentEfo = data;
        },

        SET_META(state, data) {
            state.meta = data
        },
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

        cargaLayoutEfos(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Actualizar lista de EFOS ",
                    text: "¿Está seguro de que desea actualizar la lista de EFOS?",
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
                                .post(URI + 'layout', payload.data, payload.config)
                                .then(r => r.data)
                                .then(data => {
                                    if(data.length > 0){
                                        swal("No se pudo actualizar lista de EFOS por: "+data, {
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
                                        swal("Lista de EFOS actualizada correctamente"+data, {
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

        index(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
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
        obtenerInformeCFD(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'obtener-informe', payload)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        calcularFechasLimite(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Calcular fechas límite de aclaración ante el SAT",
                    text: "¿Está seguro de que desea calcular las fechas?",
                    icon: "warning",
                    closeOnClickOutside: false,
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Calcular',
                            closeModal: false,
                        }
                    }
                })
                .then((value) => {
                    if (value) {
                        axios
                            .post(URI + 'calcular-fechas-limite', payload)
                            .then(r => r.data)
                            .then((data) => {
                                swal("Fechas calculadas correctamente", {
                                    icon: "success",
                                    timer: 1500,
                                    buttons: false
                                }).then(() => {
                                    resolve(data);
                                })
                            })
                            .catch(error => {
                                reject(error)
                            });
                    } else {
                        reject();
                    }
                });
            });
        },
        obtenerInformeCFDDesglosado(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'obtener-informe-desglosado', payload)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        descargarInformeCFDIDesglosado(context, payload){
            var urr = URI +  'obtener-informe-cfdi-desglosado?'+ 'access_token=' + this._vm.$session.get('jwt');
            var win = window.open(urr, "_blank");

            win.onbeforeunload = () => {
                swal("Informe descargado correctamente.", {
                    icon: "success",
                    timer: 2000,
                    buttons: false
                })
            }
        },
    },

    getters: {
        efos(state) {
            return state.efos
        },
        meta(state) {
            return state.meta
        }
    }
}
