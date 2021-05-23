const URI = '/api/remesas/proyecto/';
export default {
    namespaced: true,
    state: {
        proyectos: [],
        currentProyecto: null,
        meta: {},
    },

    mutations: {
        SET_PROYECTOS(state, data) {
            state.proyectos = data;
        },

        SET_META(state, data) {
            state.meta = data;
        },

        UPDATE_PROYECTO(state, data) {
            state.proyectos = state.proyectos.map(proyecto => {
                if (proyecto.id === data.id) {
                    return Object.assign({}, proyecto, data)
                }
                return proyecto
            })
            state.currentProyecto = state.currentProyecto ? data : null;
        },

        SET_PROYECTO(state, data) {
            state.currentProyecto = data;
        }
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
                        reject(error);
                    });
            });
        },
        paginate (context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'paginate', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            })
        },

        find(context, payload) {
            return new Promise((resolve, reject) => {
                axios.get(URI + payload.id, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },

        update(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Actualizar cantidad límite de remesas extraordinarias",
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
                                .patch(URI + payload.id, payload.data,{ params: payload.params } )
                                .then(r => r.data)
                                .then(data => {
                                    swal("Límite de remesas actualizado correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    })
                                        .then(() => {
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
        proyectos(state) {
            return state.proyectos
        },

        meta(state) {
            return state.meta
        },

        currentProyecto(state) {
            return state.currentProyecto
        }
    }
}
