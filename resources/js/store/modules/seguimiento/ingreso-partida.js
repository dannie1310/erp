const URI = '/api/seguimiento/ingreso-partida/';

export default {
    namespaced: true,
    state: {
        partidas: [],
        currentPartida: null,
        meta: {},
    },

    mutations: {
        SET_PARTIDAS(state, data) {
            state.partidas = data;
        },

        SET_PARTIDA(state, data) {
            state.currentPartida = data;
        },

        SET_META(state, data) {
            state.meta = data;
        },
    },

    actions: {
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
        find (context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id, { params: payload.params })
                    .then(r => r.data)
                    .then((data) => {
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
                    title: "Registrar la Partida",
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
                }).then((value) => {
                    if (value) {
                        axios
                            .post(URI, payload)
                            .then(r => r.data)
                            .then(data => {
                                swal("Partida registrada correctamente", {
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
        }
    },

    getters: {
        partidas(state) {
            return state.partidas;
        },
        meta(state) {
            return state.meta;
        },
        currentPartida(state) {
            return state.currentPartida;
        },
    }
}
