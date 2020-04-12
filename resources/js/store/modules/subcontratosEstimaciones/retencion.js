const URI = '/api/subcontratos-estimaciones/retencion/';
export default{
    namespaced: true,
    state: {
        retenciones: [],
        currentRetencion: null,
        meta: {}
    },
    mutations: {
        SET_RETENCIONES(state, data){
            state.retenciones = data
        },

        SET_META(state, data){
            state.meta = data
        },

        SET_RETENCION(state, data){
            state.currentRetencion = data
        },
        INSERT_RETENCION(state, data){
            state.retenciones = state.retenciones.concat(data);
        },
        DELETE_RETENCION(state, id) {
            state.retenciones = state.retenciones.filter(retencion => {
                return retencion.id != id
            });
        }
    },
    actions: {
        delete(context, id) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Eliminar Retención",
                    text: "¿Está seguro de que deseas eliminar la retención?",
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
                                    swal("Retención eliminada correctamente", {
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
                    title: "Registrar Retención",
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
                            swal("Retención registrada correctamente", {
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
        retenciones(state) {
            return state.retenciones
        },

        meta(state) {
            return state.meta
        },

        currentRetencion(state) {
            return state.currentRetencion
        }
    }
}
