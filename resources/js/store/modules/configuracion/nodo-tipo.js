const URI = '/api/configuracion/nodo-tipo/';

export default {
    namespaced: true,
    state: {
        nodos: [],
        currentNodo: null,
        meta: {}
    },
    mutations: {
        SET_NODOS(state, data) {
            state.nodos = data
        },

        SET_NODO(state, data) {
            state.currentNodo = data;

        },
        SET_META(state, data){
            state.meta = data
        },
        UPDATE_NODO(state, data){
            state.nodos = state.nodos.map(nodo => {
                if(nodo.id === data.id){
                    return Object.assign({}, nodo, data)
                }
                return nodo
            })
            state.currentNodo = data ;
        },
        UPDATE_ATTRIBUTE(state, data) {
            state.currentNodo[data.attribute] = data.value
        }
    },
    actions: {
        delete(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Eliminar asignación",
                    text: "¿Está seguro de que deseas eliminar la asignación?",
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
                                .delete(URI + payload.id)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Asignación eliminada correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    }).then(() => {
                                        resolve(data);
                                    })
                                })
                                .catch(error => {
                                    reject(error);
                                })
                        }
                    });
            });
        },
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
        find(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id, { params: payload.params })
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
                    title: "Registrar Nodo",
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
                }})
                .then((value) => {
                    if (value) {
                        axios
                            .post(URI, payload)
                            .then(r => r.data)
                            .then(data => {
                                swal("Nodo registrado correctamente", {
                                    icon: "success",
                                    timer: 1500,
                                    buttons: false
                                }).then((data) => {
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

        asignaciones(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id + '/asignaciones', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
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
    },

    getters: {
        nodos(state) {
            return state.nodos;
        },
        meta(state) {
            return state.meta;
        },
        currentNodo(state) {
            return state.currentNodo;
        }
    }

}
