const URI = '/api/contratos/invitacion-cotizar/';

export default {
    namespaced: true,
    state: {
        invitaciones: [],
        currentInvitacion: null,
        meta: {}
    },

    mutations: {
        SET_INVITACIONES(state, data) {
            state.invitaciones = data
        },
        SET_INVITACION(state, data)
        {
            state.currentInvitacion = data;
        },
        SET_META(state, data) {
            state.meta = data;
        },
        UPDATE_INVITACION(state, data){
            state.invitaciones = state.invitaciones.map(invitacion => {
                if(invitacion.id === data.id){
                    return Object.assign({}, invitacion, data)
                }
                return invitacion
            })
            state.currentInvitacion = data ;
        },
        UPDATE_ATTRIBUTE(state, data) {
            state.currentInvitacion[data.attribute] = data.value
        },
        DELETE_INVITACION(state, id){
            state.invitaciones = state.invitaciones.filter(invitacion => {
                return invitacion.id != id
            });
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
        store(context,payload){

            return new Promise((resolve, reject) => {
                swal({
                    title: "Enviar invitación a cotizar",
                    text: "¿Está seguro de que la información es correcta?",
                    icon: "info",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Enviar',
                            closeModal: false,
                        }
                    }                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI, payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Invitación a cotizar registrada correctamente", {
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
        storeInvitacionContraOferta(context,payload){

            return new Promise((resolve, reject) => {
                swal({
                    title: "Enviar invitación a contraofertar",
                    text: "¿Está seguro de que la información es correcta?",
                    icon: "info",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Enviar',
                            closeModal: false,
                        }
                    }                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI+'contraoferta/', payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Invitación a contraofertar generada correctamente", {
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
        update(context, payload){
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Estás seguro?",
                    text: "Actualizar Invitación a Cotizar",
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
                                .patch(URI + payload.id, payload.data)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Invitación a cotizar actualizada correctamente", {
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

        eliminar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Eliminar Invitación a Cotizar",
                    text: "¿Está seguro de que desea eliminar esta invitación a cotizar?",
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
                                .delete(URI + payload.id, { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Invitación a cotizar eliminada correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    }).then(() => {
                                        resolve(data);
                                    })
                                })
                                .catch(error =>  {
                                    reject(error);
                                });
                        } else {
                            reject();
                        }
                    });
            });
        }
    },

    getters: {
        invitaciones(state) {
            return state.invitaciones
        },
        meta(state) {
            return state.meta
        },
        currentInvitacion(state) {
            return state.currentInvitacion;
        }
    }
}
