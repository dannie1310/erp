const URI = '/api/compras/asignacion/';
export default{
    namespaced: true,
    state: {
        asignaciones: [],
        currentAsignacion: null,
        meta: {}
    },

    mutations: {
        SET_ASIGNACIONES(state, data){
            state.asignaciones = data
        },

        SET_META(state, data){
            state.meta = data
        },

        SET_ASIGNACION(state, data){
            state.currentAsignacion = data
        },

        UPDATE_ASIGNACIONES(state, data) {
            state.asignaciones = state.asignaciones.map(inventario => {
                if (asignacion.id === data.id) {
                    return Object.assign({}, asignacion, data)
                }
                return asignacion
            })
            state.currentAsignacion != null ? data : null;
        }
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
        delete(context, payload) {            
            return new Promise((resolve, reject) => {
                swal({
                    title: "Eliminar Asignación de Proveedor",
                    text: "¿Está seguro/a de que desea eliminar la asignación?",
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
                                    swal("Asignación eliminada correctamente", {
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
        generarOC(context, payload){
            return new Promise((resolve, reject) => {
                swal({
                    title: "Generar Orden de Compra",
                    text: "¿Está seguro/a de que desea generar la(s) orden(es) de compra?",
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
                                .post(URI + 'generarOC', payload.data, payload.config)
                                .then(r => r.data)
                                .then(data => {
                                    resolve(data);
                                })
                                .catch(error => {
                                    reject(error);
                                })
                        }
                    });
            });
        },
        cargaManualLayout(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Cargar Layout manual de Asignación",
                    text: "¿Está seguro/a de que desea generar Asignación?",
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
                                        swal("No se pudieron insertar las siguientes asignaciones:"+data, {
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
                                        swal("Asignaciones registrados correctamente:"+data, {
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
                        reject(error)
                    })
            });
        },
        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Asignación de Proveedores",
                    text: "¿Está seguro/a de que quiere registrar registrar la asignación de proveedores?",
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
                                    swal("Asignación de proveedores registrada correctamente", {
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
        pdf_marbetes(context, payload) {
            var URL = '/api/almacenes/inventario-fisico/' + payload.id +'/pdf_marbetes?db=' + this._vm.$session.get('db') + '&idobra=' + this._vm.$session.get('id_obra') + '&access_token=' + this._vm.$session.get('jwt');
            var win = window.open(URL, "_blank");
            win.onbeforeunload = ()=> {
                swal("Marbetes descargados correctamente.", {
                    icon: "success",
                    timer: 2000,
                    buttons: false
                })
            }
        },
        descargaLayout(context, payload){
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'descargaLayout/'+ payload.id, { params: payload.params, responseType:'blob', })
                    .then(r => r.data)
                    .then(data => {
                        const url = window.URL.createObjectURL(new Blob([data],{ type: 'text/csv' }));
                        const link = document.createElement('a');
                        link.href = url;
                        link.setAttribute('download', 'Layout-'+payload.id+'.csv');
                        document.body.appendChild(link);
                        link.click();
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },
    },

    getters: {
        asignaciones(state) {
            return state.asignaciones
        },

        meta(state) {
            return state.meta
        },

        currentAsignacion(state) {
            return state.currentAsignacion
        }
    }
}
