const URI = '/api/concursos/concurso/';
export default{
    namespaced: true,
    state: {
        concursos: [],
        currentConcurso: null,
        currentParticipante: null,
        meta: {}
    },

    mutations: {
        SET_CONCURSOS(state, data){
            state.concursos = data
        },

        SET_META(state, data){
            state.meta = data
        },

        SET_CONCURSO(state, data){
            state.currentConcurso = data
        },

        SET_PARTICIPANTE(state, data){
            state.currentParticipante = data
        },

        UPDATE_CONCURSOS(state, data) {
            state.concursos = state.concursos.map(c => {
                if (c.id === data.id) {
                    return Object.assign({}, c, data)
                }
                return c
            })
            state.currentConcurso != null ? data : null;
        }
    },

    actions: {
        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Concurso",
                    text: "¿Está seguro/a de que desea registrar el concurso?",
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
                                    swal("Concurso registrado correctamente", {
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
        update(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Actualizar datos del concurso",
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
                                swal("El concurso se ha actualizado correctamente", {
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
        findParticipante(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id+'/participante/'+payload.id_participante, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        guardaParticipante(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Estás seguro?",
                    text: "Agregar participante",
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
                            .post(URI + payload.id +'/participante', payload.data,{ params: payload.params } )
                            .then(r => r.data)
                            .then(data => {
                                swal("El participante se ha agregado correctamente", {
                                    icon: "success",
                                    timer: 1500,
                                    buttons: false
                                })
                                    .then(() => {
                                        context.commit('UPDATE_CONCURSOS',data);
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

        updateParticipante(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Estás seguro?",
                    text: "Editar datos del participante",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Editar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .patch(URI + payload.id +'/participante/'+payload.id_participante, payload.data,{ params: payload.params } )
                                .then(r => r.data)
                                .then(data => {
                                    swal("El participante se ha actualizado correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    })
                                        .then(() => {
                                            context.commit('UPDATE_CONCURSOS',data);
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
        updateParticipanteDirecto(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .patch(URI + payload.id +'/participante/'+payload.id_participante, payload.data,{ params: payload.params } )
                    .then(r => r.data)
                    .then(data => {
                        context.commit('UPDATE_CONCURSOS',data);
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },

        quitaParticipante(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Estás seguro?",
                    text: "Eliminar el participante",
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
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .delete(URI + payload.id +'/participante/'+payload.id_participante, payload.data,{ params: payload.params } )
                                .then(r => r.data)
                                .then(data => {
                                    swal("El participante se ha eliminado correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    })
                                        .then(() => {
                                            context.commit('UPDATE_CONCURSOS',data);
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

        cerrar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Cerrar Concurso",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Cerrar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .patch(URI + payload.id + '/cerrar', payload.data, { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Concurso cerrado correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    }).then(() => {
                                        context.commit('UPDATE_CONCURSOS',data);
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
        concursos(state) {
            return state.concursos
        },

        meta(state) {
            return state.meta
        },

        currentConcurso(state) {
            return state.currentConcurso
        },

        currentParticipante(state) {
            return state.currentParticipante
        }
    }
}
