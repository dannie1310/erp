const URI = '/api/almacenes/ajuste-inventario/positivo/';
export default{
    namespaced: true,
    state: {
        ajustes: [],
        currentAjuste: null,
        meta: {}
    },

    mutations: {
        SET_AJUSTES(state, data){
            state.ajustes = data
        },

        SET_META(state, data){
            state.meta = data
        },

        SET_AJUSTE(state, data){
            state.currentAjuste = data
        },

        UPDATE_AJUSTE(state, data) {
            state.ajustes = state.ajustes.map(ajuste => {
                if (ajuste.id === data.id) {
                    return Object.assign({}, ajuste, data)
                }
                return ajuste
            })
            if (state.currentAjuste) {
                state.currentAjuste = data
            }
        },

        UPDATE_ATTRIBUTE(state, data) {
            state.currentAjuste[data.attribute] = data.value
        },
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
                    title: "Registrar el ajuste positivo (+)",
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
                                    swal("Ajuste positivo registrado correctamente", {
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
        }
    },

    getters: {
        ajustes(state) {
            return state.ajustes
        },

        meta(state) {
            return state.meta
        },

        currentAjuste(state) {
            return state.currentAjuste
        }
    }
}