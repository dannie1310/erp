const URI = '/api/fiscal/informes/';
export default {
    namespaced: true,
    state: {

    },

    mutations: {

    },

    actions: {
        cargaCuentasBalanzaPorLayout(context, payload) {
            return new Promise((resolve, reject) => {

                swal({
                    title: "Cargar Cuentas de Costo",
                    text: "¿Está seguro de que deseas cargar las cuentas de costo para la empresa?",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Cargar',
                            closeModal: false,
                        }
                    },
                    dangerMode: true,
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI + 'cargar-cuentas-balanza-layout', payload.data, payload.config)
                                .then(r => r.data)
                                .then((data) => {
                                    swal("Cuentas Cargadas Correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    }).then(() => {
                                        resolve(data);
                                    })
                                })
                                .catch(error => {
                                    reject(error)
                                })
                        }
                    });
                });
        },
    },

    getters: {
        CFDSAT(state) {
            return state.CFDSAT
        },

        meta(state) {
            return state.meta
        },

        currentCFDSAT(state) {
            return state.currentCFDSAT
        }
    }
}
