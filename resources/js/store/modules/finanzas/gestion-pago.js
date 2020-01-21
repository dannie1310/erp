const URI = '/api/finanzas/gestion-pago/';

export default {
    namespaced: true,
    state: {
        bitacora: [],
        meta: {}
    },
    mutations: {
        SET_BITACORA(state, data) {
            state.bitacora = data
        }
    },
    actions: {
        cargarBitacora(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'bitacora', payload.data, payload.config)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        salir(context, payload){
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registro de Pagos con Bitácora Bancaria (SANTANDER)",
                    text: "¿Está seguro de que desea salir? Perderá los cambios no guardados.",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Salir',
                            closeModal: true,
                        }
                    },
                    dangerMode: true,
                })
                    .then((value) => {
                        if (value) {
                            resolve(null);
                        }
                    });
            });
        },
        store(context, payload){
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Pagos con Bitácora Bancaria (SANTANDER)",
                    text: "¿Está seguro de que la información es correcta?",
                    icon: "info",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Registrar Pagos',
                            closeModal: false,
                        }
                    }
                }).then((value) => {
                    if (value) {
                        axios
                            .post(URI + 'registrar_pagos', payload)
                            .then(r => r.data)
                            .then(data => {
                                swal("Pagos con Bitácora Bancaria (SANTANDER) registrados correctamente", {
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
        }
    },
    getters: {
        bitacora(state) {
            return state.bitacora;
        }
    },
}
