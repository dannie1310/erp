const URI = '/api/almacenes/inventario-fisico/';
export default{
    namespaced: true,
    state: {
        inventarios: [],
        currentInventario: null,
        meta: {}
    },

    mutations: {
        SET_INVETARIOS(state, data){
            state.inventarios = data
        },

        SET_META(state, data){
            state.meta = data
        },

        SET_INVETARIO(state, data){
            state.currentInventario = data
        },

        UPDATE_INVENTARIOS(state, data) {
            state.inventarios = state.inventarios.map(inventario => {
                if (inventario.id === data.id) {
                    return Object.assign({}, inventario, data)
                }
                return inventario
            })
            state.currentInventario != null ? data : null;
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
        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar inventario fisico",
                    text: "¿Está seguro/a de que quiere registrar un nuevo inventario físico?",
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
                                    swal("Inventario físico registrado correctamente", {
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
        pdf_marbetes(context, payload) {
            var URL = '/api/almacenes/inventario-fisico/' + payload.id +'/pdf_marbetes?db=' + this._vm.$session.get('db') + '&idobra=' + this._vm.$session.get('id_obra') + '&access_token=' + this._vm.$session.get('jwt');
            var win = window.open(URL, "_blank");
            win.onbeforeunload = ()=> {
                swal("Marbetes descargados correctamente.", {
                    icon: "success",
                    timer: 2000,
                    buttons: false
                })
            }
        },
        descargaLayout(context, payload){
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'descargaLayout/'+ payload.id, { params: payload.params, responseType:'blob', })
                    .then(r => r.data)
                    .then(data => {
                        const url = window.URL.createObjectURL(new Blob([data],{ type: 'text/csv' }));
                        const link = document.createElement('a');
                        link.href = url;
                        link.setAttribute('download', 'Layout-'+payload.id+'.csv');
                        document.body.appendChild(link);
                        link.click();
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },
        descargar_resumen_conteos(contest, payload){
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id + '/descargar_resumen_conteo', { params: payload.params, responseType:'blob', })
                    .then(r => r.data)
                    .then(data => {
                        const url = window.URL.createObjectURL(new Blob([data],{ type: 'text/csv' }));
                        const link = document.createElement('a');
                        link.href = url;
                        link.setAttribute('download', 'Layout-'+payload.id+'.csv');
                        document.body.appendChild(link);
                        link.click();
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
                    text: "Cerrar Inventario Físico",
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
                                .patch(URI + payload.id + '/actualizar', payload.data, { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Inventario Físico cerrado correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    }).then(() => {
                                        context.commit('UPDATE_INVENTARIOS',data);
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
        inventarios(state) {
            return state.inventarios
        },

        meta(state) {
            return state.meta
        },

        currentInventario(state) {
            return state.currentInventario
        }
    }
}
