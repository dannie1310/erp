const URI = '/api/presupuesto/concepto/';
export default {
    namespaced: true,
    state: {
        conceptos: [],
        currentConcepto: null,
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
                                context.commit("UPDATE_CONCEPTO", {id : data.id, activo : data.activo});
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
    },

    getters: {
        conceptos(state) {
            return state.conceptos
        },
        meta(state) {
            return state.meta
        },
        currentConcepto(state) {
            return state.currentConcepto
        }
    }
}
