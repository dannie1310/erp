const URI = '/api/AUDITORIA/asignacion-permisos/';

export default {
    namespaced: true,
    state: {
        Permisos: [],
        currentPermisos: null,
        meta: {}
    },

    mutations: {
        SET_PERMISOS(state, data) {
            state.Permisos = data
        },

        SET_CONTRATO_PROYECTADOS(state, data) {
            state.currentPermisos = data
        },

        SET_META(state, data) {
            state.meta = data
        },
        DELETE_CONTRATO_PROYECTADO(state, id) {
            state.Permisos = state.Permisos.filter((cp) => {
                return cp.id !== id;
            })
            if (state.currentPermisos && state.currentPermisos.id === id) {
                state.currentPermisos = null;
            }
        },


        UPDATE_CONTRATO_PROYECTADOS(state, data) {
            state.Permisos = state.Permisos.map(contrato => {
                if (contrato.id === data.id) {
                    return Object.assign({}, contrato, data)
                }
                return contrato
            })
            state.currentPermisos != null ? data : null;
        }
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
        obras(context, payload = {}) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'obras', payload.config)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    });
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

        getArea(payload = {}) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'getArea', { params: payload.params })
                    .then(r => r.data)
                    .then((data) => {

                        resolve(data.data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },

        actualiza(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Estás seguro?",
                    text: "Actualizar Contrato Proyectado",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Actualizar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .patch(URI + payload.id + '/actualizar', payload.data, { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Contrato Proyectado actualizado correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    }).then(() => {
                                        context.commit('UPDATE_CONTRATO_PROYECTADOS',data);
                                        resolve(data);
                                    })
                                })
                                .catch(error => {
                                    reject(error);
                                })
                        } else {
                            reject();
                        }
                    });
            });
        },
    },
    getters: {
        Permisos(state) {
            return state.Permisos
        },

        meta(state) {
            return state.meta
        },
        currentPermisos(state) {
            return state.currentPermisos
        }
    }
}