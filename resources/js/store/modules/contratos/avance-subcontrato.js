const URI = '/api/contratos/avance-subcontrato/';

export default {
    namespaced: true,
    state: {
        avances: [],
        currentAvance: null,
        meta: {}
    },

    mutations: {
        SET_AVANCES(state, data) {
            state.avances = data
        },
        SET_AVANCE(state, data) {
            state.currentAvance = data;
        },
        SET_META(state, data) {
            state.meta = data
        },
    },

    actions: {
        paginate (context, payload){
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
            });
        },
        find (context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI+payload.id, {params: payload.params})
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Avance de Subcontrato",
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
                                    swal("Avance de Subcontrato registrado correctamente", {
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
        obtenerAvance(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id+'/obtenerAvance', { params: payload.params })
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
    },

    getters: {
        avances(state) {
            return state.avances
        },
        currentAvance(state) {
            return state.currentAvance
        },
        meta(state) {
            return state.meta
        }
    }
}
