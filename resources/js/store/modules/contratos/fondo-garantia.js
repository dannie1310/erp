const URI = '/api/contratos/fondo-garantia/';

export default {
    namespaced: true,
    state: {
        fondosGarantia: [],
        currentFondoGarantia: null,
        meta: {},
    },

    mutations: {
        SET_FONDOS_GARANTIA(state, data) {
            state.fondosGarantia = data
        },

        SET_FONDO_GARANTIA(state, data) {
            state.currentFondoGarantia = data
        },

        SET_META(state, data) {
            state.meta = data
        },

        UPDATE_ATTRIBUTE(state, data) {
            _.set(state.currentFondoGarantia, data.attribute, data.value);
        },

        UPDATE_FONDO_GARANTIA(state, data) {
            state.fondosGarantia = state.fondosGarantia.map(fondoGarantia => {
                if (fondoGarantia.id === data.id) {
                    return Object.assign({}, fondoGarantia, data)
                }
                return fondoGarantia
            })
            state.currentFondoGarantia = data;
        }
    },

    actions: {
        paginate(context, payload) {
            return new Promise((resolve, reject) => {
                axios.get(URI + 'paginate', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                       resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },
        fetch(context, payload) {
            axios.get(URI, { params: payload })
                .then(r => r.data)
                .then((data) => {
                    context.commit('SET_FONDOS_GARANTIA', data.data)
                })
        },
        /*limpia(context){
            context.commit('SET_FONDO_GARANTIA', null);
        },*/
        find(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id, {params: payload.params})
                    .then(r => r.data)
                    .then((data) => {
                        /*context.commit('SET_FONDO_GARANTIA', data)*/
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },
        ajustar_saldo(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Ajustar saldo de fondo de garantía",
                    text: "¿Estás seguro/a de que la información es correcta?",
                    icon: "info",
                    buttons: ['Cancelar',
                        {
                            text: "Si, Ajustar",
                            closeModal: false,
                        }
                    ]
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI + payload.id+'/ajustar_saldo', payload.data,{ params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal({
                                        title: "Ajuste exitoso",
                                        text: " ",
                                        icon: "success",
                                        timer: 3000,
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
        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Generar fondo de garantía",
                    text: "¿Estás seguro/a de que la información es correcta?",
                    icon: "info",
                    buttons: ['Cancelar',
                        {
                            text: "Si, Generar",
                            closeModal: false,
                        }
                    ]
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI, payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal({
                                        title: "Generación exitosa",
                                        text: " ",
                                        icon: "success",
                                        timer: 3000,
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
        fondosGarantia(state) {
            return state.fondosGarantia
        },

        meta(state) {
            return state.meta
        },

        currentFondoGarantia(state) {
            return state.currentFondoGarantia
        }
    }
}