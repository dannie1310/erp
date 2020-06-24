const URI = '/api/sucursal/';

export default {
    namespaced: true,
    state: {
        sucursales: [],
        currentSucursal: null,
        meta: {}
    },

    mutations: {
        SET_SUCURSALES(state, data) {
            state.sucursales = data;
        },

        SET_SUCURSAL(state, data) {
            state.currentSucursal = data;
        },
        SET_META(state, data) {
            state.meta = data;
        },
        UPDATE_ATTRIBUTE(state, data) {
            _.set(state.currentSucursal, data.attribute, data.value);
        },

        UPDATE_SUCURSAL(state, data) {
            state.sucursales = state.sucursales.map(sucursal=> {
                if(sucursal.id === data.id){
                    return Object.assign({}, sucursal, data)
                }
                return sucursal
            })
            state.currentSucursal = data ;
        },

        INSERT_SUCURSAL(state, data){
            state.sucursales = state.sucursales.concat(data);
        },

        DELETE_SUCURSAL(state, id) {
            state.sucursales = state.sucursales.filter(sucursal => {
                return sucursal.id != id
            });
        }

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
                        reject(error);
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
                        reject(error);
                    });
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
                    });
            });
        },
        delete(context, id) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Eliminar Sucursal",
                    text: "¿Está seguro de que deseas eliminar la sucursal?",
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
                                .delete(URI + id)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Sucursal eliminada correctamente", {
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
                    title: "Registrar Sucursal",
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
                    }                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI, payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Sucursal registrado correctamente", {
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

        update(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Actualizar Sucursal",
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
                                    swal("Sucursal actualizada correctamente", {
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

    },

    getters: {
        sucursales(state) {
            return state.sucursales
        },

        meta(state) {
            return state.meta
        },

        currentSucursal(state) {
            return state.currentSucursal
        }
    }
}
