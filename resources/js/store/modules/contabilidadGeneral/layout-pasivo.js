const URI = '/api/contabilidad-general/layout-pasivo/';
export default {
    namespaced: true,
    state: {
        layouts: [],
        currentLayout: null,
        actualizando:false,
        meta: {},
        //currentPasivo : null,
    },

    mutations: {
        SET_LAYOUTS(state, data) {
            state.layouts = data;
        },

        SET_META(state, data) {
            state.meta = data;
        },

        SET_ACTUALIZANDO(state, data){
            state.actualizando = data
        },

        UPDATE_LAYOUT(state, data) {
            state.layouts = state.layouts.map(layout => {
                if (layout.id === data.id) {
                    return Object.assign({}, layout, data)
                }
                return layout
            })
            state.currentLayout = state.currentLayout ? data : null;
        },

        SET_LAYOUT(state, data) {
            state.currentLayout = data;
        },

        /*SET_PASIVO(state, data) {
            state.currentPasivo = data;
        }*/
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

        findCFDI(context, payload) {
            return new Promise((resolve, reject) => {
                axios.post(URI + payload.id_pasivo + '/lista-cfdi-asociar', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },
        asociarCFDI(context, payload) {
            return new Promise((resolve, reject) => {
                axios.post(URI + payload.id + '/asociar-cfdi', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },
        cargaLayout(context, payload){
            return new Promise((resolve, reject) => {
                swal({
                    title: "Cargar Layout de Pasivos",
                    text: "¿Está seguro de que desea cargar el layout?",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Cargar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI + 'cargar-layout', payload.data, payload.config)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Archivo cargado correctamente", {
                                        icon: "success",
                                        timer: 2000,
                                        buttons: false
                                    }).then(() => {
                                        resolve(data);
                                    })
                                })
                                .catch(error => {
                                    reject('Archivo no procesable');
                                })
                        }
                    });
            });
        },
        descargaLayoutIFS(context, payload) {
            return new Promise((resolve, reject) => {
                axios.get(URI + payload.id+"/valida-descargar-layout-ifs", {})
                    .then(r => r.data)
                    .then(data => {
                        if(data.respuesta_inconsistencia_saldo && data.respuesta_coincidencia_con_cfdi){
                            var urr = URI + payload.id + '/descargar-layout-ifs?access_token=' + this._vm.$session.get('jwt');
                            var win = window.open(urr, "_blank");

                            win.onbeforeunload = () => {
                                swal("Layout descargado correctamente", {
                                    icon: "success",
                                    timer: 2000,
                                    buttons: false
                                })
                            }
                        }else if(data.respuesta_inconsistencia_saldo && !data.respuesta_coincidencia_con_cfdi){
                            swal("Error","Algunos pasivos de la carga tienen diferencia en los datos respecto al CFDI que le corresponde, favor de corregir.", "error")

                        }else if(!data.respuesta_inconsistencia_saldo && data.respuesta_coincidencia_con_cfdi){
                            swal("Error","Algunos pasivos de la carga tienen un saldo mayor que el monto de la factura, favor de corregir.", "error")

                        }else{
                            swal("Error","Algunos pasivos de la carga tienen diferencia en los datos respecto al CFDI que le corresponde" +
                                "y un saldo mayor que el monto de la factura, favor de corregir.", "error")
                        }
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },

    },

    getters: {
        layouts(state) {
            return state.layouts
        },

        meta(state) {
            return state.meta
        },

        currentLayout(state) {
            return state.currentLayout
        },

        actualizando(state) {
            return state.actualizando
        },

        /*currentPasivo(state) {
            return state.currentPasivo
        },*/
    }
}
