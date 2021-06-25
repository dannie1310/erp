const URI = '/api/contabilidad-general/lista-empresa/';
export default {
    namespaced: true,
    state: {
        listaEmpresas: [],
        currentEmpresa: null,
        meta: {},
    },

    mutations: {
        SET_EMPRESAS(state, data) {
            state.listaEmpresas = data;
        },

        SET_META(state, data) {
            state.meta = data;
        },

        SET_EMPRESA(state, data) {
            state.currentEmpresa = data;
        },
        UPDATE_EMPRESA(state, data) {
            state.listaEmpresas = state.listaEmpresas.map(empresa => {
                if (empresa.id === data.id) {
                    return Object.assign({}, empresa, data)
                }
                return empresa
            })
            state.currentEmpresa = state.currentEmpresa ? data : null;
        },
    },

    actions: {
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

        asignados(context, payload) {
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

        consolidar(context, payload) {

            return new Promise((resolve, reject) => {
                swal({
                    title: "Actualizar Empresa Consolidadora",
                    text: "¿Está seguro/a de que desea actualizar las empresas que consolida?",
                    icon: "info",
                    closeOnClickOutside: false,
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Actualizar',
                            closeModal: false,
                        }
                    },
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .patch(URI + payload.id + '/consolidar', { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Empresas consolidadas correctamente", {
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
                axios.get(URI + payload.id, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },

        update(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Guardar cambios de la Empresa",
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
                                    swal("Empresa Actualizada Correctamente", {
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
        updateDirecto(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .patch(URI + payload.id, payload.data, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })

            });
        },
    },

    getters: {
        empresas(state) {
            return state.listaEmpresas
        },

        meta(state) {
            return state.meta
        },

        currentEmpresa(state) {
            return state.currentEmpresa
        }
    }
}
