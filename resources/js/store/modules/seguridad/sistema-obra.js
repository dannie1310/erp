const URI = '/api/SEGURIDAD_ERP/sistema/'
export default {
    namespaced: true,
    state: {
        sistemas: []
    },
   mutations: {
        SET_SISTEMAS(state, data) {
            state.sistemas = data;
        }
    },

    actions: {
        index(context, payload = {}) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI, { params: payload.params })
                    .then(r => r.data)
                    .then((data) => {

                        resolve(data.data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        find(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id, payload.config)
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },
        getSistemasObra(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'sistemas-obra', payload)
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    });
            });
        },
        asignarSistemas(context, payload = {}) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Habilitar/Deshabilitar Sistemas",
                    text: "¿Estás seguro/a de que la información es correcta?",
                    icon: "info",
                    closeOnClickOutside: false,
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Continuar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI + 'asignacion-sistemas', payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("La acción fue aplicada con éxito", {
                                        icon: "success",
                                        timer: 2000,
                                        buttons: false
                                    }).then(() => {
                                        resolve(data);
                                    })
                                })
                                .catch(error =>  {
                                    reject(error);
                                });
                        }else{
                            reject();

                        }
                    });
            });
        },
    },
    getters: {
        sistemas(state) {
            return state.sistemas;
        }
    }
}