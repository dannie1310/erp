const URI = '/api/padron-proveedores/archivo/';

export default {
    namespaced: true,
    state: {
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
            state.archivos.splice(data.index, 0, {info:true, especificacion:data.text, id_area:data.id_area});
        },
        DELETE_ARCHIVO(state, data){
            state.archivos.splice(data.index, 1);
        },
    },

    actions: {
        cargarArchivo(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Actualizar archivo de expediente.",
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
                                .post(URI + 'cargarArchivo', payload.data,{ params: payload.params } )
                                .then(r => r.data)
                                .then(data => {
                                    swal("Archivo del expediente actualizado correctamente", {
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
        cargarArchivoZIP(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Actualizar archivo de expediente.",
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
                                .post(URI + 'cargarArchivoZIP', payload.data,{ params: payload.params } )
                                .then(r => r.data)
                                .then(data => {
                                    swal("Archivo del expediente actualizado correctamente", {
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
        getArchivos(context, payload){
            return new Promise((resolve, reject) => {
                axios
                    .get(URI +  'getArchivosPrestadora',payload)
                    .then(r => r.data)
                    .then(data => {
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
                                .delete(URI + payload.id, {params: payload.params})
                                .then(r => r.data)
                                .then(data => {
                                    swal("Archivo eliminado correctamente", {
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
