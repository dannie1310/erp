const URI = '/api/finanzas/pago/carga-masiva/';

export default {
    namespaced: true,
    state: {
        layout: [],
        currentLayout: null,
        meta: {}
    },

    mutations: {
        SET_LAYOUTS(state, data) {
            state.layout = data
        },
        SET_LAYOUT(state, data) {
            state.currentLayout = data;
        },
        SET_META(state, data) {
            state.meta = data
        },
    },

    actions: {
        paginate (context, payload){
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'paginate', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },
        cargarLayout(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'layout', payload.data, payload.config)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        salir(context, payload){
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registro de Pagos con Carga Masiva",
                    text: "¿Está seguro/a de que desea salir? Perderá los cambios no guardados.",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Salir',
                            closeModal: true,
                        }
                    },
                    dangerMode: true,
                })
                    .then((value) => {
                        if (value) {
                            resolve(null);
                        }
                    });
            });
        },
        store(context, payload){
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Carga Masiva",
                    text: "¿Estás seguro/a de que la información es correcta?",
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
                }).then((value) => {
                    if (value) {
                        axios
                            .post(URI, payload)
                            .then(r => r.data)
                            .then(data => {
                                swal("Carga Manual registrada correctamente", {
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
        autorizar(context, payload){
            return new Promise((resolve, reject) => {
                swal({
                    title: "Autorización de layouts registrados",
                    text: "¿Está seguro/a de que desea autorizar este layout?",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Autorizar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .get(URI + payload.id + '/autorizar', {params: payload.params})
                                .then(r => r.data)
                                .then(data => {
                                    swal("Layout autorizado correctamente", {
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
        descarga_layout(context, payload){
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'descarga_layout', { params: payload.params, responseType:'blob', })
                    .then(r => r.data)
                    .then(data => {
                        const url = window.URL.createObjectURL(new Blob([data],{ type: 'text/csv' }));
                        const link = document.createElement('a');
                        link.href = url;
                        link.setAttribute('download', 'LayoutPendienesPagos_' + this._vm.$session.get('db') + '.csv');
                        document.body.appendChild(link);
                        link.click();
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        }
    },

    getters: {
        layouts(state) {
            return state.layout;
        },
        currentLayout(state) {
            return state.currentLayout;
        },
        meta(state) {
            return state.meta
        },
    }
}