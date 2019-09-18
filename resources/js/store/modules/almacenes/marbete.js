const URI = '/api/almacenes/marbete/';
export default{
    namespaced: true,
    state: {
        marbete: [],
        currentMarbete: null,
        meta: {}
    },

    mutations: {
        SET_MARBETES(state, data){
            state.marbetes= data
        },

        SET_META(state, data){
            state.meta = data
        },

        SET_MARBETE(state, data){
            state.currentMarbete = data
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
    },

    actions: {
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
