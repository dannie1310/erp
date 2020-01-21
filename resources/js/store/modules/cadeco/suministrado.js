const URI = '/api/suministrado/';


export default {
    namespaced: true,
    state: {
        suministrados: [],
        currentSuministrado: null,
        meta:{}
    },
    mutations:{
        SET_SUMINISTRADOS(state, data){
            state.suministrados = data;
        },
        SET_SUMINISTRADO(state,data){
            state.currentSuministrado = data;
        },
        SET_META(state,meta){
            state.meta = data;
        },

        INSERT_SUMINISTRADO(state, data){
            state.suministrados = state.suministrados.concat(data);
        },

        DELETE_SUMINISTRADO(state, id) {
            state.suministrados = state.suministrados.filter(suministrado => {
                return suministrado.id_material != id
            });
        }
    },
    actions: {
        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Suministrado",
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
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI, payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Registro realizado correctamente", {
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
        delete(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Eliminar Suministrado",
                    text: "¿Está seguro de que deseas eliminar el suministro?",
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
                                    swal("Suministro eliminado correctamente", {
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
    },

    getters: {
        suministrados(state) {
            return state.suministrados;
        },
        currentSuministrado(state) {
            return state.currentSuministrado;
        }
    }
}