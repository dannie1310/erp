const URI = '/api/proveedor-contatista/';

export default {
    namespaced: true,
    state: {
        proveedor_contratistas: [],
        currentProveeedor: null,
        meta: {}
    },

    mutations: {
        SET_PROVEEDOR_CONTRATISTAS(state, data) {
            state.proveedor_contratistas = data;
        },

        SET_PROVEEDOR_CONTRATISTA(state, data) {
            state.currentProveeedor = data;
        },

        SET_META(state, data) {
            state.meta = data;
        },
        UPDATE_PROVEEDOR_CONTRATISTA(state, data) {
            state.proveedor_contratistas = state.proveedor_contratistas.map(proveedor_contratista=> {
                if(proveedor_contratista.id === data.id){
                    return Object.assign({}, proveedor_contratista, data)
                }
                return proveedor_contratista
            })
            state.currentProveeedor = data ;
        },
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
        store(context,payload){
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Proveedor / Contratista",
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
                                    swal("Proveedor / Contratista registrado correctamente", {
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
                    text: "Actualizar Proveedor/Contratista",
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
                                    swal("Proveedor/Contratista actualizado correctamente", {
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
        proveedorContratistas(state) {
            return state.proveedor_contratistas;
        },

        meta(state) {
            return state.meta;
        },

        currentProveeedor(state) {
            return state.currentProveeedor;
        }
    }
}
