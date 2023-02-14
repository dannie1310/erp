const URI = '/api/fiscal/proveedor-rep/';
export default {
    namespaced: true,
    state: {
        proveedores_rep: [],
        currentProveedorREP: null,
        meta: {},
    },

    mutations: {
        SET_PROVEEDORES_REP(state, data) {
            state.proveedores_rep = data;
        },

        SET_META(state, data) {
            state.meta = data;
        },

        SET_PROVEEDOR_REP(state, data) {
            state.currentProveedorREP = data;
        }
    },

    actions: {
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
                    });
            });
        },

        index(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI, { params: payload.params })
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },

        paginate (context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'paginate', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        context.commit("SET_PROVEEDORES_REP", data.data);
                        context.commit("SET_META", data.meta);
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            })
        },

        proveedoresRepPendienteXls(context, payload){
            var search = '?';

            if (typeof payload.params.rfc_proveedor !== 'undefined') {
                search = search + 'rfc_proveedor='+ payload.params.rfc_proveedor + '&';
            }
            if (typeof payload.params.proveedor !== 'undefined') {
                search = search + 'proveedor='+ payload.params.proveedor + '&';
            }
            if (typeof payload.params.cantidad_cfdi !== 'undefined') {
                search = search + 'cantidad_cfdi='+ payload.params.cantidad_cfdi + '&';
            }
            if (typeof payload.params.total_cfdi !== 'undefined') {
                search = search + 'total_cfdi='+ payload.params.total_cfdi + '&';
            }
            if (typeof payload.params.total_rep !== 'undefined') {
                search = search + 'total_rep='+ payload.params.total_rep + '&';
            }

            if (typeof payload.params.pendiente_rep !== 'undefined') {
                search = search + 'pendiente_rep='+ payload.params.pendiente_rep + '&';
            }
            if (typeof payload.params.ultima_ubicacion_sao !== 'undefined') {
                search = search + 'ultima_ubicacion_sao='+ payload.params.ultima_ubicacion_sao + '&';
            }
            if (typeof payload.params.ultima_ubicacion_contabilidad !== 'undefined') {
                search = search + 'ultima_ubicacion_contabilidad='+ payload.params.ultima_ubicacion_contabilidad + '&';
            }

            var urr = URI + 'proveedores-rep-pendiente-xls'+ search + '&access_token=' + this._vm.$session.get('jwt');
            var win = window.open(urr, "_blank");

            win.onbeforeunload = () => {
                swal("Archivo descargado correctamente.", {
                    icon: "success",
                    timer: 2000,
                    buttons: false
                })
            }
        },

        enviarInvitacion(context,payload){

            return new Promise((resolve, reject) => {
                swal({
                    title: "Enviar comunicado",
                    text: "¿Está seguro que desea enviar el comunicado?",
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
                        console.log(payload);
                        if (value) {
                            axios
                                .post(URI + payload.id + "/enviar-comunicado", payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Comunicado enviado correctamente", {
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
        proveedores_rep(state) {
            return state.proveedores_rep
        },

        meta(state) {
            return state.meta
        },

        currentProveedorREP(state) {
            return state.currentProveedorREP
        }
    }
}
