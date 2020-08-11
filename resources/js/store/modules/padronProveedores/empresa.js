const URI = '/api/padron-proveedores/empresa/';

export default {
    namespaced: true,
    state: {
        empresas: [],
        currentEmpresa: null,
        meta: {}
    },

    mutations: {
        SET_EMPRESAS(state, data) {
            state.empresas = data;
        },

        SET_EMPRESA(state, data) {
            state.currentEmpresa = data;
        },

        SET_CUENTA_EMPRESA(state, data) {
            state.empresas.forEach(e => {
                if(e.id == data.empresa.id) {
                    e.cuentasEmpresa.data.push(data);
                }
            });
        },

        SET_META(state, data) {
            state.meta = data;
        }
    },

    actions: {
        paginate(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'paginate', {params: payload.params})
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
                        reject(error);
                    });
            });
        },
        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Iniciar Expediente de Proveedor",
                    text: "¿Está seguro de que la información es correcta?",
                    icon: "info",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Iniciar Expediente',
                            closeModal: false,
                        }
                    }
                }).then((value) => {
                    if (value) {
                        axios
                            .post(URI, payload)
                            .then(r => r.data)
                            .then(data => {
                                swal("Expediente iniciado correctamente", {
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
    },

    getters: {
        empresas(state) {
            return state.empresas;
        },

        meta(state) {
            return state.meta;
        },

        currentEmpresa(state) {
            return state.currentEmpresa;
        }
    }
}
