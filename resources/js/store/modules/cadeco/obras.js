const URI = '/api/obra/';

export default {
    namespaced: true,

    state: {
        obras: []
    },

    mutations: {
        fetch(state, obras) {
            state.obras = obras;
        }
    },

    actions: {
        fetch (context, payload = { }){
            axios.get('/api/auth/obras', { params: payload.params})
                .then(res => {
                    context.commit('fetch', res.data)
                })
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
                    });
            });
        },

        update(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Estás seguro?",
                    text: "Actualizar Configuración de Obra",
                    icon: "info",
                    buttons: {
                        cancel: {
                            text: 'Cancelar'
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
                                .post(URI + payload.id, payload.data, payload.config)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Configuración actualizada correctamente", {
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
                        } else {
                            resolve();
                        }
                    });
            });
        },
    },

    getters: {
        obrasAgrupadas(state) {
            return _.groupBy(state.obras, 'base_datos');
        }
    }
}