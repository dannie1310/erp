const URI = '/api/material/';

export default {
    namespaced: true,
    state: {
        materiales: [],
        currentMaterial: null,
        meta: {}
    },

    mutations: {
        SET_MATERIALES(state, data) {
            state.materiales = data
        },

        SET_MATERIAL(state, data) {
            state.currentMaterial = data;
        },

        SET_META(state, data) {
            state.meta = data
        },

        UPDATE_MATERIAL(state, data) {
            state.materiales = state.materiales.map(material => {
                if (material.id === data.id) {
                    return Object.assign({}, material, data)
                }
                return material
            })
            state.currentMaterial = state.currentMaterial ? data : null;
        },
    },

    actions: {
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

        almacen(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI+'almacen', { params: payload.params })
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
        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Material",
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
                                    swal("Material registrado correctamente", {
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
        materiales(state) {
            return state.materiales
        },
        meta(state) {
            return state.meta
        },
        currentMaterial(state) {
            return state.currentMaterial;
        }

    }
}
