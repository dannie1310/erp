const URI = '/api/almacenes/marbete/';
export default{
    namespaced: true,
    state: {
        marbetes: [],
        currentMarbete: null,
        meta: {}
    },

    mutations: {
        SET_MARBETES(state, data){
            state.marbetes = data
        },
        SET_MARBETE(state,data){
            state.currentMarbete = data
        },
        SET_META(state, data){
            state.meta = data
        },
        UPDATE_MARBETE(state, data) {
            state.marbetes = state.marbetes.map(marbete => {
                if (marbete.id === data.id) {
                    return Object.assign({}, marbete, data)
                }
                return marbete
            })
            if (state.currentMarbete) {
                state.currentMarbete = data
            }
        },

        UPDATE_ATTRIBUTE(state, data) {
            state.currentMarbete[data.attribute] = data.value
        },

        DELETE_MARBETE(state, id){
            state.marbetes = state.marbetes.filter(marbete => {
               return marbete.id != id
            });
        }
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
        store(context, payload){

            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Marbete",
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
                    }                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI, payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Marbete registrado correctamente", {
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
        eliminar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Eliminar Marbete",
                    text: "¿Estás seguro/a de que desea eliminar este Marbete?",
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
                                    swal("Marbete eliminado correctamente", {
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
        marbetes(state) {
            return state.marbetes
        },

        meta(state) {
            return state.meta
        },

        currentMarbete(state) {
            return state.currentMarbete
        }
    }
}
