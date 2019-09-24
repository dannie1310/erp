const URI = '/api/almacenes/salida/';

export default {
    namespaced: true,
    state: {
        salidas: [],
        currentSalida: null,
        meta: {}
    },

    mutations: {
        SET_SALIDAS(state, data) {
            state.salidas = data
        },

        SET_SALIDA(state, data) {
            state.currentSalida = data;
        },

        SET_META(state, data) {
            state.meta = data
        },

        UPDATE_SALIDA(state, data) {
            state.salidas = state.salidas.map(salida => {
                if (salida.id === data.id) {
                    return Object.assign([], salida, data)
                }
                return salida
            })
            if (state.currentSalida) {
                state.currentSalida = data
            }
        },

        UPDATE_ATTRIBUTE(state, data) {
            state.currentSalida[data.attribute] = data.value
        },

        DELETE_SALIDA(state, id) {
            state.salidas = state.salidas.filter(salida => {
                return salida.id != id
            });
        }
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

        eliminar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Eliminar Salida/Transferencia de Almacén",
                    text: "¿Estás seguro/a de que desea eliminar esta transacción?",
                    icon: "warning",
                    closeOnClickOutside: false,
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Eliminar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .delete(URI + payload.id, { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Transacción eliminada correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    }).then(() => {
                                        resolve(data);
                                    })
                                })
                                .catch(error =>  {
                                    reject(error);
                                });
                        } else {
                            reject();
                        }
                    });
            });
        }
    },

    getters: {
        salidas(state) {
            return state.salidas
        },
        currentSalida(state) {
            return state.currentSalida
        },
        meta(state) {
            return state.meta
        },
    }
}