const URI = '/api/seguimiento/tipo-ingreso/';

export default {
    namespaced: true,
    state: {
        conceptos: [],
        currentConcepto: null,
        meta: {},
    },

    mutations: {
        SET_CONCEPTOS(state, data) {
            state.conceptos = data;
        },

        SET_CONCEPTO(state, data) {
            state.currentConcepto = data;
        },

        SET_META(state, data) {
            state.meta = data;
        },
    },

    actions: {
        index(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        store(context, payload){
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar el Concepto",
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
                }).then((value) => {
                    if (value) {
                        axios
                            .post(URI, payload)
                            .then(r => r.data)
                            .then(data => {
                                swal("Concepto registrado correctamente", {
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
        conceptos(state) {
            return state.conceptos;
        },
        meta(state) {
            return state.meta;
        },
        currentConcepto(state) {
            return state.currentConcepto;
        },
    }
}
