const URI = '/api/contratos/estimacion/';

export default {
    namespaced: true,
    state: {
        estimaciones: []
    },

    mutations: {
        SET_ESTIMACIONES(state, data) {
            state.estimaciones = data
        }
    },

    actions: {
        pdf(context, payload) {
            axios.get(URI + payload+'/formato-orden-pago', {params: payload.params})
                .then((data) => {

                });
        },

        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Estimación",
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
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI, payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Estimación registrada correctamente", {
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
        }
    },
    getters: {
        estimaciones(state) {
            return state.estimaciones
        }
    }
}