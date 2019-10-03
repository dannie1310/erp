const URI = '/api/finanzas/pago/carga-masiva/';

export default {
    namespaced: true,
    state: {
        layout: [],
        currentLayout: null,
        meta: {}
    },

    mutations: {
        SET_LAYOUTS(state, data) {
            state.layout = data
        },
        SET_LAYOUT(state, data) {
            state.currentLayout = data;
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
        autorizar(context, payload){
            return new Promise((resolve, reject) => {
                swal({
                    title: "Autorización de layouts registrados",
                    text: "¿Está seguro/a de que desea autorizar este layout?",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Autorizar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .get(URI + payload.id + '/autorizar', {params: payload.params})
                                .then(r => r.data)
                                .then(data => {
                                    swal("Layout autorizado correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    })
                                    //     .then(() => {
                                    //     context.commit('UPDATE_DISTRIBUCION', data);
                                    //     resolve(data);
                                    // })
                                })
                                .catch(error => {
                                    reject(error);
                                })
                        }
                    });
            });
        },
    },

    getters: {
        layouts(state) {
            return state.layout;
        },
        currentLayout(state) {
            return state.currentLayout;
        },
        meta(state) {
            return state.meta
        },
    }
}
