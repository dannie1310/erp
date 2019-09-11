const URI = '/api/almacenes/inventario-fisico/';
export default{
    namespaced: true,
    state: {
        inventarios: [],
        currentInventario: null,
        meta: {}
    },

    mutations: {
        SET_INVETARIOS(state, data){
            state.inventarios = data
        },

        SET_META(state, data){
            state.meta = data
        },

        SET_INVETARIO(state, data){
            state.currentInventario = data
        },

        // UPDATE_AJUSTE(state, data) {
        //     state.ajustes = state.ajustes.map(ajuste => {
        //         if (ajuste.id === data.id) {
        //             return Object.assign({}, ajuste, data)
        //         }
        //         return ajuste
        //     })
        //     if (state.currentAjuste) {
        //         state.currentAjuste = data
        //     }
        // },
        //
        // UPDATE_ATTRIBUTE(state, data) {
        //     state.currentAjuste[data.attribute] = data.value
        // },
    },

    actions: {
        paginate(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'paginate', {params: payload.params})
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
                    title: "Registrar inventario fisico",
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
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI, payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Inventario fisico registrado correctamente", {
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
        inventarios(state) {
            return state.inventarios
        },

        meta(state) {
            return state.meta
        },

        currentInventario(state) {
            return state.currentInventario
        }
    }
}