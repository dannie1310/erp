const URI = '/api/almacenes/ajuste-inventario/';

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
        eliminar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Eliminar Ajuste de Inventario",
                    text: "¿Estás seguro/a de que desea eliminar este Ajuste de Inventario?",
                    icon: "warning",
                    closeOnClickOutside: false,
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Eliminar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .delete(URI + payload.id, { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Ajuste de Inventario eliminado correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    }).then(() => {
                                        resolve(data);
                                    })
                                })
                                .catch(error =>  {
                                    reject(error);
                                });
                        } else {
                            reject();
                        }
                    });
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
