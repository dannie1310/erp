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
                    text: "¿Estás seguro/a de que quieres registrar un nuevo inventario físico?",
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
            console.log(win.responseType);
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
        }
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
