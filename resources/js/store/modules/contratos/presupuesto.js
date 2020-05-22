const URI = '/api/contratos/presupuesto/';

export default {
    namespaced: true,
    state: {
        presupuestos: [],
        currentPresupuesto: null,
        meta: {}
    },

    mutations: {
        SET_PRESUPUESTOS(state, data) {
            state.presupuestos = data
        },

        SET_META(state, data) {
            state.meta = data;
        },

        SET_PRESUPUESTO(state, data) {
            state.currentPresupuesto = data;
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
        delete(context, payload) {            
            return new Promise((resolve, reject) => {
                swal({
                    title: "Eliminar Presupuesto Contratista",
                    text: "¿Está seguro/a de que desea eliminar presupuesto?",
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
                                    swal("Presupuesto eliminado correctamente", {
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
        update(context, payload)
        {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Estás seguro?",
                    text: "Actualizar Presupuesto Contratista",
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
                                .patch(URI + payload.id, payload.post)
                                .then(r => r.data)
                                .then(data => {
                                    swal("El Presupuesto Contratista se ha actualizado correctamente", {
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
        find(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        }
    },

    getters: {
        presupuestos(state) {
            return state.presupuestos
        },

        meta(state) {
            return state.meta
        },

        currentPresupuesto(state) {
            return state.currentPresupuesto;
        }
    }
}
