const URI = '/api/acarreos/origen/';

export default {
    namespaced: true,
    state: {
        origenes: [],
        currentOrigen: '',
        meta:{}
    },

    mutations: {
        SET_ORIGENES(state, data) {
            state.origenes = data;
        },
        SET_ORIGEN(state, data) {
            state.currentOrigen = data;
        },
        SET_META(state, data) {
            state.meta = data;
        },

        UPDATE_ATTRIBUTE(state, data) {
            _.set(state.currentOrigen, data.attribute, data.value);
        },

        UPDATE_ORIGEN(state, data) {
            state.origenes = state.origenes.map(origen => {
                if (origen.id === data.id) {
                    return Object.assign({}, origen, data)
                }
                return origen
            })
            state.currentOrigen = data;
        },
},

    actions: {
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
        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Origen",
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
                                    swal("Origen registrado correctamente", {
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
        activar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Activar el Origen",
                    text: "¿Está seguro de que deseas activar el origen?",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Activar',
                            closeModal: false,
                        }
                    },
                    dangerMode: true,
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .get(URI + payload.id+'/activar', { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Origen activado correctamente", {
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
        desactivar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Desactivar el Origen",
                    text: "¿Está seguro de que deseas desactivar el origen?",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Desactivar',
                            closeModal: false,
                        }
                    },
                    dangerMode: true,
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .get(URI + payload.id+'/desactivar', { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Origen desactivado correctamente", {
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
    },

    getters: {
        origenes(state) {
            return state.origenes
        },
        currentOrigen(state) {
            return state.currentOrigen
        },
        meta(state) {
            return state.meta;
        },
    }
}
