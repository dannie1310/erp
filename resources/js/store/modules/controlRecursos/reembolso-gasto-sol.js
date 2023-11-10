const URI = '/api/control-recursos/reembolso-gasto-sol/';

export default {
    namespaced: true,
    state: {
        reembolsos: [],
        currentReembolso: null,
        meta: {}
    },

    mutations: {
        SET_REEMBOLSOS(state, data) {
            state.reembolsos = data
        },
        SET_REEMBOLSO(state, data)
        {
            state.currentReembolso = data;
        },
        SET_META(state, data) {
            state.meta = data;
        },
        DELETE_REEMBOLSO(state, id) {
            state.reembolsos = state.reembolsos.filter(d => {
                return d.id != id
            });
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
                    })
            });
        },
        update(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Actualizar el Reembolso de Gastos",
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
                                .patch(URI + payload.id, payload.data,{ params: payload.params } )
                                .then(r => r.data)
                                .then(data => {
                                    swal("Reembolso actualizado correctamente", {
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
        delete(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Eliminar Reembolso de Gastos",
                    text: "¿Está seguro de que desea eliminar el reembolso?",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Eliminar',
                            closeModal: false,
                        }
                    },
                    dangerMode: true,
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .delete(URI + payload.id, { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Reembolso de gastos eliminado correctamente", {
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
    },

    getters: {
        reembolsos(state) {
            return state.reembolsos
        },
        meta(state) {
            return state.meta
        },
        currentReembolso(state) {
            return state.currentReembolso;
        }
    }
}
