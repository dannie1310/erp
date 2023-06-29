const URI = '/api/contabilidad-general/poliza/';
export default {
    namespaced: true,
    state: {
        polizas: [],
        currentPoliza: null,
        meta: {},

        posibles_cfdi:[],
        currentPosibleCFDI :null,
    },

    mutations: {

        SET_POLIZA(state, data) {
            state.currentPoliza = data;
            state.posibles_cfdi = state.currentPoliza && state.currentPoliza.posibles_cfdi ? state.currentPoliza.posibles_cfdi.data : null;
        },

        SET_POLIZAS(state, data) {
            state.polizas = data;
        },

        SET_META(state, data) {
            state.meta = data;
        },

        UPDATE_POLIZA(state, data) {
            state.polizas = state.polizas.map(poliza => {
                if (poliza.id === data.id) {
                    return Object.assign({}, poliza, data)
                }
                return poliza
            })
            state.posibles_cfdi = state.posibles_cfdi && data.posibles_cfdi.data? data.posibles_cfdi.data : null;
            state.currentPoliza = state.currentPoliza ? data : null;
        },

        UPDATE_ATTRIBUTE(state, data) {
            _.set(state.currentPoliza, data.attribute, data.value);
        },

        UPDATE_ATTRIBUTE_POSIBLE_CFDI(state, data) {
            _.set(state.currentPosibleCFDI, data.attribute, data.value);
        },

        SET_POSIBLES_CFDI(state, data) {
            state.posibles_cfdi = data;
        },

        SET_POSIBLE_CFDI(state, data) {
            state.currentPosibleCFDI = data;
        },

        UPDATE_POSIBLE_CFDI(state, data) {
            state.posibles_cfdi = state.posibles_cfdi.map(cfdi => {
                if (cfdi.id === data.id) {
                    return Object.assign({}, cfdi, data)
                }
                return cfdi
            })
            state.currentPosibleCFDI = state.currentPosibleCFDI ? data : null;
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

        find(context, payload) {
            return new Promise((resolve, reject) => {
                axios.get(URI + payload.id, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },

        findCfdi(context, payload) {
            return new Promise((resolve, reject) => {
                axios.post(URI + payload.id + '/lista-cfdi-asociar', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },

        descargaZip(context, payload){
            let filtros = 0;
            var search = 'id_empresa=' + payload.params.id_empresa + '&caida=' + payload.tipo + '&';
            if (typeof payload.params.ejercicio !== 'undefined') {
                search = search + 'ejercicio='+ payload.params.ejercicio + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.periodo !== 'undefined') {
                search = search + 'periodo='+ payload.params.periodo + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.tipo !== 'undefined') {
                search = search + 'tipo='+ payload.params.tipo + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.folio !== 'undefined') {
                search = search + 'folio='+ payload.params.folio + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.concepto !== 'undefined') {
                search = search + 'concepto='+ payload.params.concepto + '&';
                filtros = +filtros + 1;
            }

            if(filtros == 0){
                swal({
                    title: "Aviso",
                    text: "Debe utilizar al menos un filtro de búsqueda",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cerrar',
                            visible: true
                        }
                    }
                })
            }else{
                var urr = URI +  'descargar-pdf?'+ search+'access_token=' + this._vm.$session.get('jwt');

            var win = window.open(urr, "_blank");

                win.onbeforeunload = () => {
                    swal("Archivo ZIP descargado correctamente.", {
                        icon: "success",
                        timer: 2000,
                        buttons: false
                    })
                }
            }


        },

        findEdit(context, payload) {
            return new Promise((resolve, reject) => {
                axios.get(URI + payload.id + '/editar', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },

        update(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Guardar cambios de la Póliza",
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
                                .patch(URI + payload.id, payload.data, { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Póliza Actualizada correctamente", {
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
        actualizaCFDI(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'actualizar-cfdi', payload)
                    .then(r => r.data)
                    .then((data) => {
                        swal(data.mensaje, {
                            icon: data.icon,
                            timer: 3000,
                            buttons: false
                        })
                            .then(() => {
                                resolve(data);
                            })
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        busquedaExcel(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'busquedaExcel', payload.data, {params: payload.params})
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                        var urr = URI +  'descargar-zip?nombreZip='+ data+'&access_token=' + this._vm.$session.get('jwt');

                        var win = window.open(urr, "_blank");

                        win.onbeforeunload = () => {
                            swal("Archivo ZIP descargado correctamente.", {
                                icon: "success",
                                timer: 2000,
                                buttons: false
                            })
                        }
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },
        asociarCFDI(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Asociar CFDI",
                    text: "¿Está seguro de asociar los CFDI a la póliza?",
                    icon: "info",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Asociar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI+"asociar-cfdi", payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("CFDI asociados.", {
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
    },

    getters: {
        polizas(state) {
            return state.polizas
        },

        meta(state) {
            return state.meta
        },

        currentPoliza(state) {
            return state.currentPoliza
        },

        posibles_cfdi(state) {
            return state.posibles_cfdi
        },

        currentPosibleCFDI(state) {
            return state.currentPosibleCFDI
        },
    }
}
