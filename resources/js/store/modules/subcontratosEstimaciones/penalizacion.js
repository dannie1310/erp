const URI = '/api/subcontratos-estimaciones/penalizacion/';
export default{
    namespaced: true,
    state: {
        penalizaciones: [],
        currentPenalizacion: null,
        meta: {}
    },
    mutations: {
        SET_PENALIZACIONES(state, data){
            state.penalizaciones = data
        },

        SET_META(state, data){
            state.meta = data
        },

        SET_PENALIZACION(state, data){
            state.currentPenalizacion = data
        },
        INSERT_PENALIZACION(state, data){
            state.penalizaciones = state.penalizaciones.concat(data);
        },
        DELETE_PENALIZACION(state, id) {
            state.penalizaciones = state.penalizaciones.filter(penalizacion => {
                return penalizacion.id != id
            });
        }
    },
    actions: {
        delete(context, id) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Eliminar Penalización",
                    text: "¿Está seguro de que deseas eliminar la penalización?",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Eliminar',
                            closeModal: false,
                        }
                    },
                    dangerMode: true,
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .delete(URI + id)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Penalización eliminada correctamente", {
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
                })
            });
        },
        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Penalización",
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
                            swal("Penalización registrada correctamente", {
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
                    })
            });
        },
    },
    getters: {
        penalizaciones(state) {
            return state.penalizaciones
        },

        meta(state) {
            return state.meta
        },

        currentPenalizacion(state) {
            return state.currentPenalizacion
        }
    }
}
