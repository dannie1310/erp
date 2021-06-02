const URI = '/api/acarreos/material/';

export default {
    namespaced: true,
    state: {
        materiales: [],
        currentMaterial: '',
        meta:{}
    },

    mutations: {
        SET_MATERIALES(state, data) {
            state.materiales = data;
        },
        SET_MATERIAL(state, data) {
            state.currentMaterial = data;
        },
        SET_META(state, data) {
            state.meta = data;
        },

        UPDATE_ATTRIBUTE(state, data) {
            _.set(state.currentMaterial, data.attribute, data.value);
        },

        UPDATE_MATERIAL(state, data) {
            state.materiales = state.materiales.map(material => {
                if (material.id === data.id) {
                    return Object.assign({}, material, data)
                }
                return material
            })
            state.currentMaterial = data;
        },
},

    actions: {
        paginate(context, payload) {
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
            })
        },
        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Material",
                    text: "¿Está seguro de que la información es correcta?",
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
                                    swal("Material registrado correctamente", {
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
        activar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Activar el Material",
                    text: "¿Está seguro de que desea activar el material?",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Activar',
                            closeModal: false,
                        }
                    },
                    dangerMode: true,
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .get(URI + payload.id+'/activar', { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Material activado correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    }).then(() => {
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
        desactivar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Desactivar el Material",
                    text: "¿Está seguro de que desea desactivar el material?",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Desactivar',
                            closeModal: false,
                        }
                    },
                    dangerMode: true,
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .get(URI + payload.id+'/desactivar', { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Material desactivado correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    }).then(() => {
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
        // update(context, payload) {
        //     return new Promise((resolve, reject) => {
        //         swal({
        //             title: "¿Está seguro?",
        //             text: "Actualizar el origen",
        //             icon: "warning",
        //             buttons: {
        //                 cancel: {
        //                     text: 'Cancelar',
        //                     visible: true
        //                 },
        //                 confirm: {
        //                     text: 'Si, Actualizar',
        //                     closeModal: false,
        //                 }
        //             }
        //         })
        //             .then((value) => {
        //                 if (value) {
        //                     axios
        //                         .patch(URI + payload.id, payload.data,{ params: payload.params } )
        //                         .then(r => r.data)
        //                         .then(data => {
        //                             swal("Origen actualizado correctamente", {
        //                                 icon: "success",
        //                                 timer: 1500,
        //                                 buttons: false
        //                             })
        //                                 .then(() => {
        //                                     resolve(data);
        //                                 })
        //                         })
        //                         .catch(error => {
        //                             reject(error);
        //                         })
        //                 }
        //             });
        //     });
        // },
        descargaLayout(context, payload){
            var urr = URI + 'descargaLayout?db=' + this._vm.$session.get('db') + '&idobra=' + this._vm.$session.get('id_obra') + '&access_token=' + this._vm.$session.get('jwt');
            var win = window.open(urr, "_blank");

            win.onbeforeunload = () => {
                swal("Layout descargado correctamente.", {
                    icon: "success",
                    timer: 2000,
                    buttons: false
                })
            }
        },
    },

    getters: {
        materiales(state) {
            return state.materiales
        },
        currentMaterial(state) {
            return state.currentMaterial
        },
        meta(state) {
            return state.meta;
        },
    }
}
