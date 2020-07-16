const URI = '/api/fiscal/no-deducido/';
export default {
    namespaced: true,
    state: {
        noDeducidos: [],
        currentNoDeducido: null,
        meta: {},
    },
    mutations: {
        SET_NODEDUCIDOS(state, data) {
            state.noDeducidos = data;
        },
        SET_META(state, data) {
            state.meta = data;
        },
        SET_NODEDUCIDO(state, data) {
            state.currentNoDeducido = data;
        },
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
        find(context, payload) {
            return new Promise((resolve, reject) => {
                axios.get(URI + payload.id, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },
        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar los CFD No Deducidos",
                    text: "¿Está seguro de que la información es correcta?",
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
                                    swal("CFD No Deducidos registrados correctamente", {
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
        },
    },

    getters: {
        noDeducidos(state) {
            return state.noDeducidos
        },

        meta(state) {
            return state.meta
        },

        currentNoDeducido(state) {
            return state.currentNoDeducido
        }
    }
}
