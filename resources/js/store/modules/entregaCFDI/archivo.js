const URI = '/api/entrega-cfdi/archivo/';

export default {
    namespaced: true,
    state: {
        currentTransaccion: null,
        archivos: [],
        currentArchivo: null,
        meta: {}
    },

    mutations: {
        SET_ARCHIVOS(state, data) {
            state.archivos = data;
        },

        SET_ARCHIVO(state, data) {
            state.currentArchivo = data;
        },

        SET_TRANSACCION(state, data) {
            state.currentTransaccion = data;
        },

        SET_META(state, data) {
            state.meta = data;
        },

        UPDATE_ARCHIVO(state, data) {
            state.archivos = state.archivos.map(archivo => {
                if (archivo.id === data.id) {
                    return Object.assign({}, archivo, data)
                }
                return archivo
            })
            if (state.currentArchivo) {
                state.currentArchivo = data
            }
        },

        INSERT_ARCHIVO(state, data){
            state.archivos.splice(0, 0, data);
        },
        DELETE_ARCHIVO(state, id){
            state.archivos = state.archivos.filter(archivo => {
                return archivo.id != id
            });
        },
    },

    actions: {
        agregarArchivo(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Agregar archivo.",
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
                                .post(URI + 'agregarArchivo', payload.data,{ params: payload.params } )
                                .then(r => r.data)
                                .then(data => {
                                    swal("Archivo agregado correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    })
                                        .then(() => {
                                            context.commit("UPDATE_ARCHIVO", data);
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
        cargarArchivo(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Cargar archivo.",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Cargar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI + 'cargarArchivo', payload.data,{ params: payload.params } )
                                .then(r => r.data)
                                .then(data => {
                                    swal("Archivo cargado correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    })
                                        .then(() => {
                                            context.commit("INSERT_ARCHIVO", data);
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
        reemplazarArchivo(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Reemplazar archivo.",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Reemplazar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI + 'reemplazarArchivo', payload.data,{ params: payload.params } )
                                .then(r => r.data)
                                .then(data => {
                                    swal("Archivo reemplazado correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    })
                                        .then(() => {
                                            context.commit("UPDATE_ARCHIVO", data);
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

        cargarArchivoZIP(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Cargar archivo.",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Cargar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI + 'cargarArchivoZIP', payload.data,{ params: payload.params } )
                                .then(r => r.data)
                                .then(data => {
                                    swal("Archivo cargado correctamente", {
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
        getArchivosCFDI(context, payload){
            return new Promise((resolve, reject) => {
                axios
                    .get(URI  + 'cfdi/' + payload.id_cfdi,payload)
                    .then(r => r.data)
                    .then(data => {
                        context.commit("SET_ARCHIVOS", data.data);
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    });
            });
        },

        eliminar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Eliminar el Archivo",
                    text: "¿Está seguro que desea eliminar el archivo previamente cargado?",
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
                                .post(URI + 'eliminarArchivo', payload.data,{ params: payload.params } )
                                .then(r => r.data)
                                .then(data => {
                                    swal("Archivo eliminado correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    })
                                        .then(() => {
                                            context.commit("UPDATE_ARCHIVO", data);
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
        eliminarTipo(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Eliminar el Tipo de Archivo",
                    text: "¿Está seguro que desea eliminar el tipo de archivo?",
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
                                    swal("Tipo de archivo eliminado correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    }).then(() => {
                                        context.commit("DELETE_ARCHIVO", payload.id);
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
        getImagenes(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id + '/imagenes', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    });
            });
        },
    },
    getters: {
        archivos(state) {
            return state.archivos;
        },

        meta(state) {
            return state.meta;
        },

        currentArchivo(state) {
            return state.currentArchivo;
        }
    }
}
