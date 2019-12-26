const URI = '/api/contabilidad/tipo-cuenta-contable/';

export default {
    namespaced: true,
    state: {
        tipos: [],
        currentTipo: null,
        meta: {}
    },

    mutations: {
        SET_TIPOS(state, data) {
            state.tipos = data
        },

        SET_META(state, data) {
            state.meta = data
        },

        SET_TIPO(state, data) {
            state.currentTipo = data
        },

        DELETE_TIPO(state, id) {
            state.tipos = state.tipos.filter((tipo) => {
                return tipo.id !== id;
            })
            if (state.currentTipo && state.currentTipo.id === id) {
                state.currentTipo = null;
            }
        },

        UPDATE_ATTRIBUTE(state, data) {
            _.set(state.currentTipo, data.attribute, data.value);
        },

        UPDATE_TIPO(state, data) {
            state.tipos = state.tipos.map(tipo => {
                if (tipo.id === data.id) {
                    return Object.assign({}, tipo, data)
                }
                return tipo
            })
            state.currentTipo = state.currentTipo ? data : null;
        }
    },

    actions: {
        delete(context, id) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Eliminar tipo de cuenta contable",
                    text: "¿Está seguro de que deseas eliminar este tipo de cuenta contable?",
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
                                    swal("Tipo cuenta contable eliminado correctamente", {
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
        index(context, payload = {}) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI, payload.config)
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    });
            });
        },
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
                    title: "Registrar Cuenta Contable",
                    text: "¿Está seguro de que la información es correcta?",
                    icon: "info",
                    buttons: {
                        cancel:{
                            text: "Cancelar",
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
                                    swal({
                                        title: "Cuenta contable registrada correctamente",
                                        text: " ",
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
                    text: "Guardar cambios del Tipo de Cuenta Contable",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Guardar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .patch(URI + payload.id, payload.data, { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Cuenta actualizada correctamente", {
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
        tipos(state) {
            return state.tipos
        },

        meta(state) {
            return state.meta
        },

        currentTipo(state) {
            return state.currentTipo
        }
    }
}