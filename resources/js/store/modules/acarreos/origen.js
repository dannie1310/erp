const URI = '/api/acarreos/origen/';

export default {
    namespaced: true,
    state: {
        origenes: [],
        currentOrigen: '',
        meta:{}
    },

    mutations: {
        SET_ORIGENES(state, data) {
            state.origenes = data;
        },
        SET_ORIGEN(state, data) {
            state.currentOrigen = data;
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
                    })
            })
        },
        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Origen",
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
                                    swal("Origen registrado correctamente", {
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
        origenes(state) {
            return state.origenes
        },
        currentOrigen(state) {
            return state.currentOrigen
        },
        meta(state) {
            return state.meta;
        },
    }
}
