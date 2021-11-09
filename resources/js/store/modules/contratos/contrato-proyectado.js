const URI = '/api/contratos/contrato-proyectado/';

export default {
    namespaced: true,
    state: {
        contratos: [],
        currentContrato: null,
        meta: {}
    },

    mutations: {
        SET_CONTRATOS(state, data) {
            state.contratos = data
        },
        SET_CONTRATO(state, data) {
            state.currentContrato = data
        },
        SET_META(state, data) {
            state.meta = data
        },
        DELETE_CONTRATO(state, id) {
            state.contratos = state.contratos.filter(contrato => {
                return contrato.id != id
            });
        },
        UPDATE_CONTRATO(state, data) {
            state.contratos = state.contratos.map(contrato => {
                if (contrato.id === data.id) {
                    return Object.assign({}, contrato, data)
                }
                return contrato
            })
            state.currentContrato = data;
        },
        UPDATE_ATTRIBUTE(state, data) {
            state.currentContrato[data.attribute] = data.value
        },
    },

    actions: {
        cargarLayout(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'layout', payload.data, payload.config)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
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
        update(context, payload){
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Estás seguro?",
                    text: "Actualizar Contrato Proyectado",
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
                                    swal("Contrato Proyectado actualizado correctamente", {
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
        find (context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id, { params: payload.params })
                    .then(r => r.data)
                    .then((data) => {
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
                    title: "Registrar Contrato Proyectado",
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
                                    swal("Contrato proyectado registrado correctamente", {
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
        getContratos(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'getContratos', {params: payload.params})
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },
        getCotizaciones(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id + '/getCotizaciones', {params: payload.params})
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },
        getComparativaCotizaciones(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id + '/comparativa-cotizaciones', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        getArea(payload = {}) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'getArea', { params: payload.params })
                    .then(r => r.data)
                    .then((data) => {

                        resolve(data.data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        actualiza(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Actualizar Contrato Proyectado",
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
                                .patch(URI + payload.id + '/actualizar', payload.data, { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Contrato Proyectado actualizado correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    }).then(() => {
                                            context.commit('UPDATE_CONTRATO_PROYECTADOS',data);
                                            resolve(data);
                                        })
                                })
                                .catch(error => {
                                    reject(error);
                                })
                        } else {
                            reject();
                        }
                    });
            });
        },
        eliminar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Eliminar el Contrato Proyectado",
                    text: "¿Está seguro de que desea eliminar este contrato?",
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
                                    swal("Contrato eliminado correctamente", {
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
        getCuerpoCorreo(context, payload){
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id + '/getCuerpoCorreo', {  })
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
        contratos(state) {
            return state.contratos
        },
        meta(state) {
            return state.meta
        },
        currentContrato(state) {
            return state.currentContrato
        }
    }
}
