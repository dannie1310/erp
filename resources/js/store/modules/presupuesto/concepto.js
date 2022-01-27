const URI = '/api/presupuesto/concepto/';
export default {
    namespaced: true,
    state: {
        conceptos: [],
        currentConcepto: null,
        dato: null,
        responsables:[],
        meta: {},
    },

    mutations: {
        SET_CONCEPTOS(state, data) {
            state.conceptos = data;
        },
        SET_META(state, data) {
            state.meta = data;
        },
        SET_CONCEPTO(state, data) {
            state.currentConcepto = data;
        },
        SET_RESPONSABLES(state, data) {
            state.responsables = data;
        },
        SET_DATO(state, data) {
            state.dato = data;
        },
        DELETE_RESPONSABLE(state, id) {
            state.responsables = state.responsables.filter( responsable => {
                return responsable.id != id
            });
        },
        AGREGA_CONCEPTOS(state, data) {
            state.conceptos = state.conceptos.concat(data);
            state.conceptos.sort(function (a, b) {
                if (a.nivel > b.nivel) {
                    return 1;
                }
                if (a.nivel < b.nivel) {
                    return -1;
                }
                return 0;
            });
        },
        UPDATE_CONCEPTO(state, data) {
            state.conceptos = state.conceptos.map(concepto => {
                if (concepto.id === data.id) {
                    return Object.assign( concepto, data)
                }
                return concepto
            })
        },
        UPDATE_CONCEPTO_DATO(state, data) {
            state.conceptos = state.conceptos.map(concepto => {
                if (concepto.id === data.id) {
                    return Object.assign( concepto.dato, data)
                }
                return concepto
            })
        },
        OCULTA_CONCEPTOS(state, nivel) {
            state.conceptos = state.conceptos.map(concepto => {
                var nivel_recortado;
                nivel_recortado = concepto.nivel.substring(0,nivel.length);
                if (nivel_recortado === nivel && concepto.nivel != nivel) {
                    return Object.assign( concepto, {visible:0})
                }
                return concepto
            })
        },
        MUESTRA_CONCEPTOS(state, nivel) {
            state.conceptos = state.conceptos.map(concepto => {
                var nivel_recortado;
                nivel_recortado = concepto.nivel.substring(0,nivel.length);
                if (nivel_recortado === nivel && concepto.nivel != nivel) {
                    return Object.assign( concepto, {visible:1})
                }
                return concepto
            })
        },
    },

    actions: {
        find (context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id, { params: payload.params })
                    .then(r => r.data)
                    .then((data) => {
                        context.commit("SET_CONCEPTO", data);
                        context.commit("SET_CONCEPTOS", [data]);
                        if(data.responsables != undefined){
                            context.commit("SET_RESPONSABLES", data.responsables.data);
                        }
                        if(data.dato != undefined){
                            context.commit("SET_DATO", data.dato);
                        }
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        actualizaClaves(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Actualizar la clave de los conceptos",
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
                        .patch(URI + 'actualiza-claves', payload.data)
                        .then(r => r.data)
                        .then(data => {
                            swal("Conceptos actualizados correctamente", {
                                icon: "success",
                                timer: 1500,
                                buttons: false
                            })
                                .then(() => {
                                    data.data.forEach(function (dato, i) {
                                        context.commit("UPDATE_CONCEPTO", {id : dato.id, clave_concepto : dato.clave_concepto});
                                    });
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
        actualizaClave(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Actualizar la clave del concepto",
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
                                .patch(URI + 'actualiza-clave', payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Clave actualizada correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    })
                                        .then(() => {
                                            context.commit("UPDATE_CONCEPTO", {id : data.id, clave_concepto : data.clave_concepto});
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
        actualizaDatosSeguimiento(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Actualizar datos de seguimiento de concepto",
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
                                .patch(URI+ payload.id +'/'+ 'actualiza-datos-seguimiento', payload.datos)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Datos de seguimiento actualizados correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    })
                                        .then(() => {
                                            //context.commit("SET_RESPONSABLES", data.responsables.data);
                                            //context.commit("UPDATE_CONCEPTO", {id : data.id, clave_concepto : data.clave_concepto});
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
        toggleActivo(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .patch(URI +payload.id+ '/toggle-activo', payload.data)
                    .then(r => r.data)
                    .then(data => {
                        swal("Concepto actualizado correctamente", {
                            icon: "success",
                            timer: 1500,
                            buttons: false
                        })
                            .then(() => {
                                data.data.forEach(concepto =>{
                                    context.commit("UPDATE_CONCEPTO", {id : concepto.id, activo : concepto.activo});
                                });
                                resolve(data);
                            })
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },
        getRaiz(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id_padre+'/hijos', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        context.commit("SET_CONCEPTOS", data.data);
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    });
            });
        },
        getHijos(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id_padre+'/hijos', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        context.commit("UPDATE_CONCEPTO", {expandido:1,hijos_cargados:1,id:payload.id_padre});
                        context.commit("AGREGA_CONCEPTOS", data.data);
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    });
            });
        },
        ocultaHijos(context, payload) {
            return new Promise((resolve, reject) => {
                context.commit("UPDATE_CONCEPTO", {expandido:0,id:payload.id_padre});
                context.commit("OCULTA_CONCEPTOS", payload.nivel);
                resolve();
            });
        },
        muestraHijos(context, payload) {
            return new Promise((resolve, reject) => {
                context.commit("UPDATE_CONCEPTO", {expandido:1,id:payload.id_padre});
                context.commit("MUESTRA_CONCEPTOS", payload.nivel);
                resolve();
            });
        },
        storeResponsable(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Responsable",
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
                                .post(URI + 'responsable', payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Responsable registrado correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    }).then(() => {
                                        if(data.responsables != undefined){
                                            context.commit("SET_RESPONSABLES", data.responsables.data);
                                        }
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
        quitarResponsable(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Eliminar Responsable",
                    text: "¿Está seguro de eliminar a este responsable?",
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
                                .delete(URI +'responsable/'+ payload.id, { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Responsable eliminado correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    }).then(() => {
                                        context.commit("DELETE_RESPONSABLE", payload.id);
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
        },
    },

    getters: {
        conceptos(state) {
            return state.conceptos
        },
        responsables(state) {
            return state.responsables
        },
        meta(state) {
            return state.meta
        },
        currentConcepto(state) {
            return state.currentConcepto
        }
    }
}
