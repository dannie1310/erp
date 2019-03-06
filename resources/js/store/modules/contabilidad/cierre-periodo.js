const URI = '/api/contabilidad/cierre-periodo/';

export default {
    namespaced: true,
    state: {
        cierres: [],
        currentCierre: null,
        meta: {}
    },

    mutations: {
        SET_CIERRES(state, data) {
            state.cierres = data
        },

        SET_META(state, data) {
            state.meta = data
        },

        SET_CIERRE(state, data) {
            state.currentCierre = data
        },

        UPDATE_CIERRE(state, data) {
            state.cierres = state.cierres.map(cierre => {
                if (cierre.id === data.id) {
                    return Object.assign([], cierre, data)
                }
                return cierre
            })
            state.currentCierre = data
        },

        UPDATE_ATTRIBUTE(state, data) {
            state.currentCierre[data.attribute] = data.value
        }
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
                    title: "Registrar Cierre de Periodo",
                    text: "¿Estás seguro/a de que la información es correcta?",
                    icon: "info",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
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
                                    swal("Cierre de periodo registrado correctamente", {
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
                    title: "¿Estás seguro?",
                    text: "Actualizar Cierre de Periodo",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
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
                                    swal("Cierre de periodo actualizado correctamente", {
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
        }
    },

    getters: {
        cierres(state) {
            return state.cierres
        },

        meta(state) {
            return state.meta
        },

        currentCierre(state) {
            return state.currentCierre
        }
    }
}