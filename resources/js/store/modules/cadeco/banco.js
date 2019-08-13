const URI = '/api/banco/';

export default {
    namespaced: true,
    state: {
        bancos: [],
        currentBanco: null,
        meta: {}
    },

    mutations: {
        SET_BANCOS(state, data) {
            state.bancos = data;
        },

        SET_BANCO(state, data) {
            state.currentBanco = data;
        },

        SET_CUENTA_BANCO(state, data) {
            state.bancos.forEach(e => {
                if(e.id == data.banco.id) {
                    e.cuentasBanco.data.push(data);
                }
            });
        },

        SET_META(state, data) {
            state.meta = data;
        }
    },

    actions: {
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

        index(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    });
            });
        },

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
        store(context,payload){

            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Banco",
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
                    }                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI, payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Banco registrado correctamente", {
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

        },

    },

    getters: {
        bancos(state) {
            return state.bancos;
        },

        meta(state) {
            return state.meta;
        },

        currentBanco(state) {
            return state.currentBanco;
        }
    }
}
