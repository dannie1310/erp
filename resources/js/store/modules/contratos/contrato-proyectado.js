const URI = '/api/contratos/contrato-proyectado/';

export default {
    namespaced: true,
    state: {
        contratoProyectado: [],
        meta: {}
    },

    mutations: {
        SET_CONTRATO_PROYECTADO(state, data) {
            state.contratoProyectado = data
        },

        SET_META(state, data) {
            state.meta = data
        },

        APROBAR_ESTIMACION(state, id) {
            state.contratoProyectado.forEach(contProyectado => {
                if(contProyectado.id == id) {
                    contProyectado.estado = 1;
                }
            })
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

        aprobar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Estás seguro?",
                    text: "Aprobar Estimación",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Aprobar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .patch(URI + payload.id + '/aprobar')
                                .then(r => r.data)
                                .then(data => {
                                    swal("Estimación aprobada correctamente", {
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
                        } else {
                            reject();
                        }
                    });
            });
        },
    },
    getters: {
        contratoProyectado(state) {
            return state.contratoProyectado
        },

        meta(state) {
            return state.meta
        }
    }
}