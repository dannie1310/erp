const URI = '/api/seguimiento/vw-ingreso/';

export default {
    namespaced: true,
    state: {
        ingresos: [],
        currentIngreso: null,
        meta: {},
    },

    mutations: {
        SET_INGRESOS(state, data) {
            state.ingresos = data;
        },

        SET_INGRESO(state, data) {
            state.currentIngreso = data;
        },

        SET_META(state, data) {
            state.meta = data;
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
                    });
            });
        },
        envioCorreo(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Enviar el Correo de Ingreso",
                    text: "¿Está seguro de envio del ingreso?",
                    dangerMode: true,
                    icon: "info",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, reenviar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value)
                        {
                            axios
                                .patch(URI+ payload.id+'/envioCorreo',  {id:payload.id}, { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Ingreso enviado correctamente", {
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
                        else if(value == '')
                        {
                            swal("Ingrese el motivo de cancelación de la factura.",{icon: "error"});
                        }
                    });
            });
        },
    },

    getters: {
        ingresos(state) {
            return state.ingresos;
        },
        meta(state) {
            return state.meta;
        },
        currentIngreso(state) {
            return state.currentIngreso;
        },
    }
}
