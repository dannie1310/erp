const URI = '/api/contratos/asignacion-contratista/';
export default{
    namespaced: true,
    state: {
        asignaciones: [],
        currentAsignacion: null,
        meta: {}
    },
    mutations: {
        SET_ASIGNACIONES(state, data){
            state.asignaciones = data
        },

        SET_META(state, data){
            state.meta = data
        },

        SET_ASIGNACION(state, data){
            state.currentAsignacion = data
        },

        UPDATE_ASIGNACIONES(state, data) {
            state.asignaciones = state.asignaciones.map(asignacion => {
                if (asignacion.id === data.id) {
                    return Object.assign({}, asignacion, data)
                }
                return asignacion
            })
            state.currentAsignacion != null ? data : null;
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
                        reject(error)
                    })
            });
        },
        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Asignación de Contratistas",
                    text: "¿Está seguro/a de que desea registrar la asignación de contratistas?",
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
                                    swal("Asignación de contratistas registrada correctamente", {
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
        eliminar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Eliminar la Asignación a Contratista",
                    text: "¿Está seguro de que desea eliminar esta asignación?",
                    icon: "warning",
                    closeOnClickOutside: false,
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Eliminar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .delete(URI + payload.id, {params: payload.params})
                                .then(r => r.data)
                                .then(data => {
                                    swal("Asignación eliminada correctamente", {
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
                        } else {
                            reject();
                        }
                    });
            });
        },
    },
    getters: {
        asignaciones(state) {
            return state.asignaciones
        },

        meta(state) {
            return state.meta
        },

        currentAsignacion(state) {
            return state.currentAsignacion
        }
    }

}
