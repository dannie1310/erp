const URI = '/api/contabilidad-general/layout-pasivo-partida/';
export default {
    namespaced: true,
    state: {
        pasivos: [],
        currentPasivo: null,
        actualizando:false,
        meta: {},
    },

    mutations: {
        SET_PASIVOS(state, data) {
            state.pasivos = data;
        },

        SET_META(state, data) {
            state.meta = data;
        },

        SET_ACTUALIZANDO(state, data){
            state.actualizando = data
        },

        UPDATE_PASIVO(state, data) {
            state.pasivos = state.pasivos.map(pasivo => {
                if (pasivo.id === data.id) {
                    return Object.assign({}, pasivo, data)
                }
                return pasivo
            })
            state.currentPasivo = state.currentPasivo ? data : null;
        },

        SET_PASIVO(state, data) {
            state.currentPasivo = data;
        }
    },

    actions: {
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

        findCFDI(context, payload) {
            return new Promise((resolve, reject) => {
                axios.post(URI + payload.id_pasivo + '/lista-cfdi-asociar', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },
        asociarCFDI(context, payload) {
            return new Promise((resolve, reject) => {
                axios.post(URI + payload.id + '/asociar-cfdi', { params: payload.params })
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
                    text: "Actualizar Pasivo",
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
                            .patch(URI + payload.id, payload.data,{ params: payload.params } )
                            .then(r => r.data)
                            .then(data => {
                                swal("Pasivo actualizada correctamente", {
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
        pasivos(state) {
            return state.pasivos
        },

        meta(state) {
            return state.meta
        },

        currentPasivo(state) {
            return state.currentPasivo
        },

        actualizando(state) {
            return state.actualizando
        },
    }
}
