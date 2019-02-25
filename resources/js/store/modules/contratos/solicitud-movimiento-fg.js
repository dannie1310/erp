const URI = '/api/contratos/fondo-garantia/solicitud-movimiento/';

export default {
    namespaced: true,
    state: {
        solicitudes: [],
        currentSolicitud: null,
        meta: {},
    },

    mutations: {
        SET_SOLICITUDES(state, data) {
            state.solicitudes = data
        },

        SET_SOLICITUD(state, data) {
            state.currentSolicitud = data
        },

        SET_META(state, data) {
            state.meta = data
        },

        UPDATE_ATTRIBUTE(state, data) {
            _.set(state.currentSolicitud, data.attribute, data.value);
        },

        UPDATE_SOLICITUD(state, data) {
            state.solicitudes = state.solicitudes.map(solicitud => {
                if (solicitud.id === data.id) {
                    return Object.assign({}, solicitud, data)
                }
                return solicitud
            })
            state.currentSolicitud != null ? data : null;
        }
    },

    actions: {
        paginate(context, payload) {
            context.commit('SET_SOLICITUDES', [])
            axios
                .get(URI + 'paginate', {params: payload})
                .then(r => r.data)
                .then(data => {
                    context.commit('SET_SOLICITUDES', data.data)
                    context.commit('SET_META', data.meta)
                })
        },

        find(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id, {params: payload.params})
                    .then(r => r.data)
                    .then((data) => {
                        context.commit('SET_SOLICITUD', data)
                        resolve();
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },

        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Solicitud de Movimiento a Fondo de Garantía",
                    text: "¿Estás seguro/a de que la información es correcta?",
                    icon: "info",
                    buttons: ['Cancelar',
                        {
                            text: "Si, Registrar",
                            closeModal: false,
                        }
                    ]
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI, payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal({
                                        title: "Registro éxitoso",
                                        text: " ",
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
        autorizar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Autorizar Solicitud de Movimiento a Fondo de Garantía",
                    text: "¿Estás seguro/a de autorizar la solicitud de movimiento?",
                    icon: "info",
                    buttons: [
                        'Cancelar',
                        {
                            text: "Si, Autorizar",
                            closeModal: false,
                        }
                    ]
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .patch(URI+ payload.id+'/autorizar',  {id:payload.id}, { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal({
                                        title: "Autorización éxitosa",
                                        text: " ",
                                        icon: "success",
                                        timer: 3000,
                                        buttons: false
                                    }).then(() => {
                                        context.commit('UPDATE_SOLICITUD', data);
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

        cancelar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Cancelar Solicitud de Movimiento a Fondo de Garantía",
                    text: "¿Estás seguro/a de cancelar la solicitud de movimiento?",
                    dangerMode: true,
                    icon: "info",
                    content: {
                        element: "input",
                        attributes: {
                            placeholder: "Motivo de Cancelación",
                            type: "text",
                        },
                    },
                    buttons: [
                        'Cancelar',
                        {
                            text: "Si, Cancelar",
                            closeModal: false,
                        }
                    ]
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .patch(URI+ payload.id+'/cancelar',  {id:payload.id, observaciones: value}, { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal({
                                        title: "Cancelación éxitosa",
                                        text: " ",
                                        icon: "success",
                                        timer: 3000,
                                        buttons: false
                                    }).then(() => {
                                        context.commit('UPDATE_SOLICITUD', data);
                                        resolve(data);

                                    })
                                })
                                .catch(error => {
                                    reject(error);
                                });
                        } else {
                            swal("Ingrese el motivo de cancelación de la solicitud.",{icon: "error"});
                        }
                    });
            });
        },

        rechazar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Rechazae Solicitud de Movimiento a Fondo de Garantía",
                    text: "¿Estás seguro/a de rechazar la solicitud de movimiento?",
                    dangerMode: true,
                    icon: "info",
                    content: {
                        element: "input",
                        attributes: {
                            placeholder: "Motivo de Rechazo",
                            type: "text",
                        },
                    },
                    buttons: [
                        'Cancelar',
                        {
                            text: "Si, Rechazar",
                            closeModal: false,
                        }
                    ]
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .patch(URI+ payload.id+'/rechazar',  {id:payload.id, observaciones: value}, { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal({
                                        title: "Rechazo éxitoso",
                                        text: " ",
                                        icon: "success",
                                        timer: 3000,
                                        buttons: false
                                    }).then(() => {
                                        context.commit('UPDATE_SOLICITUD', data);
                                        resolve(data);

                                    })
                                })
                                .catch(error => {
                                    reject(error);
                                });
                        } else {
                            swal("Ingrese el motivo de rechazo de la solicitud.",{icon: "error"});
                        }
                    });
            });
        },

        revertir_autorizacion(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Revertir Autorización de Solicitud de Movimiento a Fondo de Garantía",
                    text: "¿Estás seguro/a de revertir la autorización de la solicitud de movimiento?",
                    dangerMode: true,
                    icon: "info",
                    content: {
                        element: "input",
                        attributes: {
                            placeholder: "Motivo de Reversión",
                            type: "text",
                        },
                    },
                    buttons: [
                        'Cancelar',
                        {
                            text: "Si, Revertir",
                            closeModal: false,
                        }
                    ]
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .patch(URI+ payload.id+'/revertir-autorizacion',  {id:payload.id, observaciones: value}, { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal({
                                        title: "Reversión éxitosa",
                                        text: " ",
                                        icon: "success",
                                        timer: 3000,
                                        buttons: false
                                    }).then(() => {
                                        context.commit('UPDATE_SOLICITUD', data);
                                        resolve(data);

                                    })
                                })
                                .catch(error => {
                                    reject(error);
                                });
                        } else {
                            swal("Ingrese el motivo para revertir la autorización de la solicitud.",{icon: "error"});
                        }
                    });
            });
        },

    },

    getters: {
        solicitudes(state) {
            return state.solicitudes
        },

        meta(state) {
            return state.meta
        },

        currentSolicitud(state) {
            return state.currentSolicitud
        }
    }
}