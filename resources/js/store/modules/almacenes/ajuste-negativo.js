const URI = '/api/almacenes/ajuste-inventario/negativo/';
export default{
    namespaced: true,
    state: {
        ajustes: [],
        currentAjuste: null,
    },

    mutations: {
        SET_AJUSTES(state, data){
            state.ajustes = data
        },

        SET_AJUSTE(state, data){
            state.currentAjuste = data
        },

        UPDATE_AJUSTE(state, data) {
            state.ajustes = state.ajustes.map(ajuste => {
                if (ajuste.id === data.id) {
                    return Object.assign({}, ajuste, data)
                }
                return ajuste
            })
            if (state.currentAjuste) {
                state.currentAjuste = data
            }
        },

        UPDATE_ATTRIBUTE(state, data) {
            state.currentAjuste[data.attribute] = data.value
        },
    },

    actions: {
        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar el ajuste negativo (-)",
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
                                    swal("Ajuste negativo registrado correctamente", {
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
        
        cargaLayout(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Cargar Layout de Ajuste (-) en el Ajuste de Inventario",
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

        find (context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI+payload.id, {params: payload.params})
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        eliminar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Eliminar Ajuste Negativo de Inventario",
                    text: "¿Está seguro de que desea eliminar este Ajuste de Inventario?",
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
                                    swal("Ajuste de Inventario eliminado correctamente", {
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
        ajustes(state) {
            return state.ajustes
        },

        currentAjuste(state) {
            return state.currentAjuste
        }
    }
}