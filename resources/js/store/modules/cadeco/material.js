const URI = '/api/material/';

export default {
    namespaced: true,
    state: {
        materiales: [],
        currentMaterial: null,
        meta: {}
    },

    mutations: {
        SET_MATERIALES(state, data) {
            state.materiales = data
        },

        SET_MATERIAL(state, data) {
            state.currentMaterial = data;
        },

        SET_META(state, data) {
            state.meta = data
        },

        UPDATE_MATERIAL(state, data) {
            state.materiales = state.materiales.map(material => {
                if (material.id === data.id) {
                    return Object.assign({}, material, data)
                }
                return material
            })
            state.currentMaterial = state.currentMaterial ? data : null;
        },
        UPDATE_ATTRIBUTE(state, data) {
            state.currentMaterial[data.attribute] = data.value
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
                        reject(error)
                    })
            });
        },

        buscarMateriales(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'buscarMateriales', { params: payload.params })
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
                    title: "Eliminar Insumo",
                    text: "¿Está seguro/a de que desea eliminar insumo?",
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
                                    swal("Insumo eliminado correctamente", {
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

        index(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        lista_materiales(context, payload) {
            var urr = URI + 'descargar_lista_material?scope=' + payload.scope + '&db=' + this._vm.$session.get('db') + '&idobra=' + this._vm.$session.get('id_obra') + '&access_token=' + this._vm.$session.get('jwt');
            var win = window.open(urr, "_blank");

            win.onbeforeunload = () => {
                swal("Lista de material descargada correctamente.", {
                    icon: "success",
                    timer: 2000,
                    buttons: false
                })
            }
        },
        almacen(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI+'almacen', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
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
                    })
            })
        },
        update(context, payload) {

            return new Promise((resolve, reject) => {
                swal({
                    title: "Actualizar Insumo",
                    text: "¿Está seguro/a de que desea actualizar insumo?",
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
                                .patch(URI + payload.id, payload.data, { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("El Insumo ha sido actualizado correctamente", {
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
        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar",
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
                                    swal("Insumo registrado correctamente", {
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
        porAlmacen(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + "porAlmacen", { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        historico(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + "historico", { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        historicoSalida(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + "historicoSalida", { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
    },

    getters: {
        materiales(state) {
            return state.materiales
        },
        meta(state) {
            return state.meta
        },
        currentMaterial(state) {
            return state.currentMaterial;
        }
    }
}
