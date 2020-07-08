const URI = '/api/fiscal/autocorreccion/';
export default {
    namespaced: true,
    state: {
        autocorrecciones: [],
        currentAutocorreccion: null,
        meta: {},
    },
    mutations: {
        SET_AUTOCORRECCIONES(state, data) {
            state.autocorrecciones = data;
        },
        SET_META(state, data) {
            state.meta = data;
        },
        SET_AUTOCORRECCION(state, data) {
            state.currentAutocorreccion = data;
        },
        UPDATE_AUTOCORRECCION(state, data) {
            state.autocorrecciones = state.autocorrecciones.map(a => {
                if (a.id === data.id) {
                    return Object.assign({}, a, data)
                }
                return a
            })
            state.currentAutocorreccion = state.currentAutocorreccion ? data : null;
        },
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
        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar la Autocorrección de los CFDS",
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
                                    swal("Autocorrección registrada correctamente", {
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
        aplicar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Aplicar Autocorrección de CFD",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Aplicar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .patch(URI + payload.id + '/aplicar', payload.data,{ params: payload.params } )
                                .then(r => r.data)
                                .then(data => {
                                    swal("Autocorrección aplicada correctamente", {
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
                        } else {
                            reject();
                        }
                    });
            });
        },
    },

    getters: {
        autocorrecciones(state) {
            return state.autocorrecciones
        },

        meta(state) {
            return state.meta
        },

        currentAutocorreccion(state) {
            return state.currentAutocorreccion
        }
    }
}
