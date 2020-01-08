const URI = '/api/SEGURIDAD_ERP/efo/';
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

        UPDATE_CONTEO(state, data) {
            state.conteos = state.conteos.map(conteo => {
                if (conteo.id === data.id) {
                    return Object.assign({}, conteo, data)
                }
                return conteo
            })
            state.currentConteo = data;
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

        cargaLayoutEfos(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Actualizar lista de EFO´s ",
                    text: "¿Está seguro de que desea actualizar la lista de EFO´s?",
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
                                .post(URI + 'layout', payload.data, payload.config)
                                .then(r => r.data)
                                .then(data => {
                                    if(data.length > 0){
                                        swal("No se pudo actualizar lista de EFO´s por: "+data, {
                                            buttons: {
                                                confirm: {
                                                    text: 'Aceptar',
                                                    closeModal: true,
                                                }
                                            }
                                        }).then(() => {
                                            resolve(data);
                                        })
                                    }else{
                                        swal("Lista de EFO´s actualizados correctamente:"+data, {
                                            icon: "success",
                                            timer: 2000,
                                            buttons: false
                                        }).then(() => {
                                            resolve(data);
                                        })
                                    }

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
