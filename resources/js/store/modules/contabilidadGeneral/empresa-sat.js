const URI = '/api/contabilidad-general/empresa-sat/';
export default {
    namespaced: true,
    state: {
        empresasSAT: [],
        currentEmpresaSAT: null,
        meta: {},
    },

    mutations: {
        SET_EMPRESAS(state, data) {
            state.empresasSAT = data;
        },

        SET_META(state, data) {
            state.meta = data;
        },

        SET_EMPRESA(state, data) {
            state.currentEmpresaSAT = data;
        },
        UPDATE_EMPRESA(state, data) {
            state.empresasSAT = state.empresasSAT.map(empresa => {
                if (empresa.id === data.id) {
                    return Object.assign({}, empresa, data)
                }
                return empresa
            })
            state.currentEmpresaSAT = state.currentEmpresaSAT ? data : null;
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
    },

    getters: {
        empresas(state) {
            return state.empresasSAT
        },

        meta(state) {
            return state.meta
        },

        currentEmpresa(state) {
            return state.currentEmpresaSAT
        }
    }
}