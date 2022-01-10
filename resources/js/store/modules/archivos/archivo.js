const URI = '/api/archivo/';

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
        cargarArchivoSC(context, payload) {
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
                                .post(URI + 'cargar-archivo-sc', payload.data,{ params: payload.params } )
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
        getArchivosTransaccion(context, payload){
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id + '/transaccion',payload)
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
        getArchivosInvitacion(context, payload){
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id + '/invitacion',payload)
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
        getArchivosTransaccionSC(context, payload){
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + payload.id + '/transaccion-sc', payload )
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
        getArchivosRelacionadosTransaccion(context, payload){
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.tipo+ '/' + payload.id + '/transaccion-relacionados',payload)
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
        getArchivosRelacionadosTransaccionSC(context, payload){
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.tipo+ '/' + payload.id + '/transaccion-relacionados-sc',payload)
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
                                .post(URI + payload.id, payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Archivo eliminado correctamente", {
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
        eliminarSC(context, payload) {
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
                                .post(URI + payload.id + "/destroy-sc", payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Archivo eliminado correctamente", {
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
        descargarArchivoInvitacion(context, payload){
            var urr = URI +  'descargar-archivo-invitacion/'+payload.id+'?access_token=' + this._vm.$session.get('jwt');
            var win = window.open(urr, "_blank");

            win.onbeforeunload = () => {
                swal("Archivo descargado correctamente.", {
                    icon: "success",
                    timer: 2000,
                    buttons: false
                })
            }
        },
        descargar(context, payload){
            var urr = URI + payload.id+ '/descargar?db=' + this._vm.$session.get('db') + '&idobra=' + this._vm.$session.get('id_obra') + '&access_token='+ this._vm.$session.get('jwt');
            var win = window.open(urr, "_blank");

            win.onbeforeunload = () => {
                swal("Archivo descargado correctamente.", {
                    icon: "success",
                    timer: 2000,
                    buttons: false
                })
            }
        },
        getArchivoInvitacion(context, payload){
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'consultar-archivo-invitacion/' + payload.id, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },
        getArchivo(context, payload){
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
        eliminarArchivoInvitacion(context, payload) {
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
                                .patch(URI + 'eliminar-archivo-invitacion/' + payload.id, payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Archivo eliminado correctamente", {
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
