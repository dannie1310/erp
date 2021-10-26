const URI = '/api/control-obra/avance/';

export default {
    namespaced: true,
    state: {
        avances: [],
        currentAvance: null,
        meta: {},
    },

    mutations: {
        SET_AVANCES(state, data) {
            state.avances = data
        },
        SET_AVANCE(state, data) {
            state.currentAvance = data;
        },
        SET_META(state, data) {
            state.meta = data;
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
        store(context,payload){
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Avance de Obra",
                    text: "¿Estás seguro/a de que la información es correcta?",
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
                                    swal("Registrar avance de obra registrada correctamente", {
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
                        reject(error)
                    })
            });
        },
        aprobar(context, payload){
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Aprobar el Avance de Obra",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Aprobar',
                            closeModal: false,
                        }
                    }
                }).then((value) => {
                    if (value) {
                        axios
                            .patch(URI + payload.id + '/aprobar', payload.data)
                            .then(r => r.data)
                            .then(data => {
                                swal("Avance de Obra aprobado correctamente", {
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
        revertir(context, payload){
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Revertir Aprobación del Avance de Obra",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Revertir',
                            closeModal: false,
                        }
                    }
                }).then((value) => {
                    if (value) {
                        axios
                            .patch(URI + payload.id + '/revertir', payload.data)
                            .then(r => r.data)
                            .then(data => {
                                swal("Avance de Obra se revirtió correctamente", {
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
        avances(state) {
            return state.avances
        },
        currentAvance(state) {
            return state.currentAvance
        },
        meta(state) {
            return state.meta
        },
    }
}
