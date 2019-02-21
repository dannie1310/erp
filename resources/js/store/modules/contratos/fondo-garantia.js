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
            context.commit('SET_FONDOS_GARANTIA', [])
            axios
                .get(URI + 'paginate', {params: payload})
                .then(r => r.data)
                .then(data => {
                    context.commit('SET_FONDOS_GARANTIA', data.data)
                    context.commit('SET_META', data.meta)
                })
        },

        find(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id, {params: payload.params})
                    .then(r => r.data)
                    .then((data) => {
                        context.commit('SET_FONDO_GARANTIA', data)
                        resolve();
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },


        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar fondo de garantía",
                    text: "¿Estás seguro/a de que la información es correcta?",
                    icon: "info",
                    buttons: ['Cancelar', 'Si, Registrar']
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI, payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Fondo de Garantía generado correctamente", {
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