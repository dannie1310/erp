const URI = '/api/fiscal/cfd-sat/';
export default {
    namespaced: true,
    state: {
        CFDSAT: [],
        currentCFDSAT: null,
        meta: {},
    },

    mutations: {
        SET_CFDSAT(state, data) {
            state.CFDSAT = data;
        },

        SET_META(state, data) {
            state.meta = data;
        },

        SET_cCFDSAT(state, data) {
            state.currentCFDSAT = data;
        },
        UPDATE_CFDSAT(state, data) {
            state.CFDSAT = state.CFDSAT.map(cfd => {
                if (cfd.id === data.id) {
                    return Object.assign({}, cfd, data)
                }
                return cfd
            })
            state.currentCFDSAT = state.currentCFDSAT ? data : null;
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
                    text: "Guardar cambios del CFD",
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
                                    swal("CFD Actualizado Correctamente", {
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
        cargarZIP(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'carga-zip', payload.data, payload.config)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        procesaDirZIPCFD(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'procesa-dir-zip-cfd', payload.data, payload.config)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        obtenerInformeCFDEmpresaMes(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'obtener-informe-empresa-mes', payload)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        getContenidoDirectorio(context, payload){
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'obtener-contenido-directorio', payload)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        }
    },

    getters: {
        CFDSAT(state) {
            return state.CFDSAT
        },

        meta(state) {
            return state.meta
        },

        currentCFDSAT(state) {
            return state.currentCFDSAT
        }
    }
}
