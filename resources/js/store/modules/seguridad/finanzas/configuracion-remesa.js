const URI = '/api/SEGURIDAD_ERP/configuracion-remesa/';

export default {
    namespaced: true,
    state: {
        currentRemesa: null,
        meta: {},
    },
    mutations: {
       SET_REMESA(state, data) {
            state.currentRemesa = data
        },

        SET_META(state, data) {
            state.meta = data
        }
    },
    actions: {
        actualizar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Estás seguro?",
                    text: "Actualizar Datos de Remesa",
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
                                .patch(URI + 'actualizar', payload.data)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Datos de remesa actualizados correctamente", {
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
                    .get(URI + 'find')
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
        meta(state) {
            return state.meta
        },

        currentRemesa(state) {
            return state.currentRemesa
        }
    }
}