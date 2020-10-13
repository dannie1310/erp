const URI = '/api/contabilidad-general/poliza/';
export default {
    namespaced: true,
    state: {
        polizas: [],
        currentPoliza: null,
        meta: {},
    },

    mutations: {
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
            state.currentPoliza = state.currentPoliza ? data : null;
        },

        SET_POLIZA(state, data) {
            state.currentPoliza = data;
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

        descargaZip(context, payload){
            console.log(payload);
            var search = 'id_empresa=' + payload.params.id_empresa + '&caida=' + payload.tipo + '&';
            if (typeof payload.params.ejercicio !== 'undefined') {
                search = search + 'ejercicio='+ payload.params.ejercicio + '&';  
            }
            if (typeof payload.params.periodo !== 'undefined') {
                search = search + 'periodo='+ payload.params.periodo + '&';  
            }
            if (typeof payload.params.tipo !== 'undefined') {
                search = search + 'tipo='+ payload.params.tipo + '&';  
            }
            if (typeof payload.params.folio !== 'undefined') {
                search = search + 'folio='+ payload.params.folio + '&';  
            }
            if (typeof payload.params.concepto !== 'undefined') {
                search = search + 'concepto='+ payload.params.concepto + '&';  
            }

            var urr = URI +  'descargar-pdf?'+ search+'access_token=' + this._vm.$session.get('jwt');

            var win = window.open(urr, "_blank");

            win.onbeforeunload = () => {
                swal("Archivo ZIP descargado correctamente.", {
                    icon: "success",
                    timer: 2000,
                    buttons: false
                })
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
        busquedaExcel(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'busquedaExcel', payload.data, {params: payload.params})
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
        polizas(state) {
            return state.polizas
        },

        meta(state) {
            return state.meta
        },

        currentPoliza(state) {
            return state.currentPoliza
        }
    }
}
