const URI = '/api/finanzas/comprobante-fondo/';


export default {
    namespaced: true,
    state: {
        fondos: [],
        currentFondo: null,
        meta:{}
    },
    mutations:{
        SET_FONDOS(state, data){
            state.fondos = data;
        },
        SET_FONDO(state,data){
            state.currentFondo = data;
        },
        SET_META(state, data) {
            state.meta = data
        },
        UPDATE_ATTRIBUTE(state, data) {
            _.set(state.currentFondo, data.attribute, data.value);
        },
        UPDATE_FONDO(state, data) {
            state.fondos = state.fondos.map(fondo => {
                if (fondo.id === data.id) {
                    return Object.assign({}, fondos, data)
                }
                return fondo
            })
            state.currentFondo = data;
        },
    },
    actions: {
        paginate (context, payload){
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
            });
        },
        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Comprobante de Fondo",
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
                                    swal("Comprobante de fondo registrado correctamente", {
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
        eliminar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Eliminar el Comprobante de Fondo",
                    text: "¿Está seguro de que desea eliminar esta comprobante de fondo?",
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
                                    swal("Comprobante de fondo eliminada correctamente", {
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
        fondos(state) {
            return state.fondos;
        },
        currentFondo(state) {
            return state.currentFondo;
        },
        meta(state) {
            return state.meta
        },
    }
}
