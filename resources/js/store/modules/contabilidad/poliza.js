const URI = '/api/contabilidad/poliza/';
export default {
    namespaced: true,
    state: {
        polizas: [],
        currentPoliza: null,
        meta: {},
    },

    mutations: {
        SET_POLIZAS(state, data) {
            state.polizas = data;
        },

        SET_META(state, data) {
            state.meta = data;
        },

        UPDATE_POLIZA(state, data) {
            state.polizas = state.polizas.map(poliza => {
                if (poliza.id === data.id) {
                    return Object.assign({}, poliza, data)
                }
                return poliza
            })
            state.currentPoliza = data;
        },

        SET_POLIZA(state, data) {
            state.currentPoliza = data;
        }
    },

    actions: {
        paginate (context, payload) {
            context.commit('SET_POLIZAS', [])
            axios
                .get(URI + 'paginate', {params: payload})
                .then(r => r.data)
                .then((data) => {
                    context.commit('SET_POLIZAS', data.data)
                    context.commit('SET_META', data.meta)
                })
        },

        find(context, payload) {
            return new Promise((resolve, reject) => {
                context.commit('SET_POLIZA', null)
                axios.get(URI + payload.id, {params: payload.params})
                    .then(r => r.data)
                    .then((data) => {
                        context.commit('SET_POLIZA', data)
                        resolve();
                    })
                    .catch(error => {
                        reject(error);
                    })
            });

        },

        update(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Estás seguro?",
                    text: "Guardar cambios de la Prepóliza",
                    icon: "warning",
                    buttons: ['Cancelar', 'Si, Guardar']
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .patch(URI + payload.id, payload.data, { params: payload.params })
                                .then(r => r.data)
                                .then((data) => {
                                    swal("Prepóliza Actualizada correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    })
                                        .then(() => {
                                            context.commit('UPDATE_POLIZA', data);
                                            resolve();
                                        })
                                })
                                .catch(error => {
                                    reject();
                                })
                        }
                    });
            });
        },

        validar(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Validar Prepóliza",
                    text: "¿Esta seguro de que deseas validar la Prepóliza?",
                    icon: "warning",
                    buttons: ["Cancelar", "Si, Validar"]
                })
                    .then((value) => {
                        if (value) {
                            axios.patch(URI + payload.id + '/validar', payload.data, { params: payload.params })
                                .then(r => r.data)
                                .then((data) => {
                                    swal("Prepóliza Validada correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    }).then(() => {
                                        context.commit('UPDATE_POLIZA', data)
                                        resolve();
                                    })
                                })
                                .catch(error => {
                                    reject(error);
                                })
                        }
                    });
            });
        },

        omitir(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Omitir Prepóliza",
                    text: "¿Esta seguro de que deseas omitir la Prepóliza?",
                    icon: "warning",
                    buttons: ["Cancelar", "Si, Omitir"]
                })
                    .then((value) => {
                        if (value) {
                            axios.patch(URI + payload.id + '/omitir', payload.data,  { params: payload.params })
                                .then(r => r.data)
                                .then((data) => {
                                    swal("Prepóliza omitida correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    }).then(() => {
                                        context.commit('UPDATE_POLIZA', data);
                                        resolve();
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
        polizas(state) {
            return state.polizas
        },

        meta(state) {
            return state.meta
        },

        currentPoliza(state) {
            return state.currentPoliza
        }
    }
}