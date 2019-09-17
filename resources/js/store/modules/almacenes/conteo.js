const URI = '/api/almacenes/conteo/';
export default{
    namespaced: true,
    state: {
        conteos: [],
        currentConteo: null,
        meta: {}
    },

    mutations: {
        SET_CONTEOS(state, data){
            state.conteos = data
        },

        SET_META(state, data){
            state.meta = data
        },

        SET_CONTEO(state, data){
            state.currentConteo = data
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

        cancelar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Cancelar Conteo",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Continuar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .get(URI + payload.id + '/cancelar', payload.data, { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Conteo cancelado correctamente", {
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

        cargaManualLayout(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Cargar Layout manual de conteo",
                    text: "¿Está seguro/a de que desea generar conteo?",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Generar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI + 'layout', payload.data, payload.config)
                                .then(r => r.data)
                                .then(data => {
                                    console.log(data);
                                    swal("No se pudieron insertar los siguientes conteos:"+data, {
                                        buttons: {
                                            confirm: {
                                                text: 'Aceptar',
                                                closeModal: true,
                                            }
                                        }
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
                    })
            });
        },

    },

    getters: {
        conteos(state) {
            return state.conteos
        },

        meta(state) {
            return state.meta
        },

        currentConteo(state) {
            return state.currentConteo
        }
    }
}