const URI = '/api/compras/almacen/entrada/';
export default{
    namespaced: true,
    state: {
        entradas: [],
        currentEntrada: null,
        meta: {}
    },

    mutations: {
        SET_ENTRADAS(state, data){
            state.entradas = data
        },

        SET_META(state, data){
            state.meta = data
        },

        SET_ENTRADA(state, data){
            state.currentEntrada = data
        },

        UPDATE_ATTRIBUTE(state, data) {
            _.set(state.currentEntrada, data.attribute, data.value);
        },

        UPDATE_ENTRADA(state, data) {
            state.entradas = state.entradas.map(entrada => {
                if (entrada.id === data.id) {
                    return Object.assign({}, entrada, data)
                }
                return entrada
            })
            state.currentEntrada = data;
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
                    })
            });
        },

        delete(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Eliminar la Entrada de Almacén",
                    text: "¿Estás seguro/a de que desea eliminar esta entrada?",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Eliminar',
                            closeModal: false,
                        }
                    },
                    dangerMode: true,
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .delete(URI + payload.id, { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Entrada de almacén eliminada correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    }).then(() => {
                                        context.commit('UPDATE_ENTRADA', data);
                                        resolve(data);
                                    })
                                })
                                .catch(error => {
                                    reject(error);
                                })
                        }
                    });
            });
        }
    },

    getters: {
        entradas(state) {
            return state.entradas
        },

        meta(state) {
            return state.meta
        },

        currentEntrada(state) {
            return state.currentEntrada
        }
    }
}