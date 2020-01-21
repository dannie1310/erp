const URI = '/api/compras/requisicion/';

export default {
    namespaced: true,
    state: {
        requisiciones: [],
        currentRequisicion: null,
        meta: {}
    },

    mutations: {
        SET_REQUISICIONES(state, data) {
            state.requisiciones = data
        },
        SET_REQUISICION(state, data)
        {
            state.currentRequisicion = data;
        },
        SET_META(state, data) {
            state.meta = data;
        },
        UPDATE_REQUISICION(state, data){
            state.requisiciones = state.requisiciones.map(requisicion => {
                if(requisicion.id === data.id){
                    return Object.assign({}, requisicion, data)
                }
                return requisicion
            })
            state.currentRequisicion = data ;
        },
        UPDATE_ATTRIBUTE(state, data) {
            state.currentRequisicion[data.attribute] = data.value
        },
        DELETE_REQUISICION(state, id){
            state.requisiciones = state.requisiciones.filter(requisicion => {
                return requisicion.id != id
            });
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
        store(context,payload){

            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Requisición de Compra",
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
                                    swal("Requisición de Compra registrada correctamente", {
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
        update(context, payload){
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Estás seguro?",
                    text: "Actualizar Requisición de Compra",
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
                                .patch(URI + payload.id, payload.data)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Requisición de Compra actualizada correctamente", {
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
                    title: "Cargar Layout de Requisición de Compra",
                    text: "¿Está seguro/a de que desea cargar partidas?",
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
                                        swal("Partidas agregadas correctamente:", {
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
        eliminar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Eliminar Requisición de Compra",
                    text: "¿Estás seguro/a de que desea eliminar esta Requisición de Compra?",
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
                                .delete(URI + payload.id, { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Requisición de Compra eliminada correctamente", {
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
        }
    },

    getters: {
        requisiciones(state) {
            return state.requisiciones
        },
        meta(state) {
            return state.meta
        },
        currentRequisicion(state) {
            return state.currentRequisicion;
        }
    }
}
