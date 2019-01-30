const URI = '/api/contabilidad/cuenta-almacen/';

export default {
    namespaced: true,
    state: {
        cuentas: [],
        currentCuenta: null,
        meta: {}
    },

    mutations: {
        SET_CUENTAS(state, data) {
            state.cuentas = data
        },

        SET_META(state, data) {
            state.meta = data
        },

        SET_CUENTA(state, data) {
            state.currentCuenta = data
        },

        UPDATE_CUENTA(state, data) {
            state.cuentas = state.cuentas.map(cuenta => {
                if (cuenta.id === data.id) {
                    return Object.assign([], cuenta, data)
                }
                return cuenta
            })
            state.currentCuenta = data
        },

        UPDATE_ATTRIBUTE(state, data) {
            state.currentCuenta[data.attribute] = data.value
        }
    },

    actions: {
        paginate (context, payload){
            axios
                .get(URI + 'paginate', { params: payload })
                .then(r => r.data)
                .then(data => {
                    context.commit('SET_CUENTAS', data.data)
                    context.commit('SET_META', data.meta)
                })
        },

        find(context, id) {
            return new Promise((resolve, reject) => {
                context.commit('SET_CUENTA', null)
                axios
                    .get(URI + id)
                    .then(r => r.data)
                    .then(data => {
                        context.commit('SET_CUENTA', data)
                        resolve();
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },

        update(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Estás seguro?",
                    text: "Actualizar Cuenta de Almacén",
                    icon: "warning",
                    buttons: ['Cancelar', 'Si, Actualizar']
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .patch(URI + payload.id, payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Cuenta actualizada correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    })
                                        .then(() => {
                                            context.commit('UPDATE_CUENTA', data);
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
        cuentas(state) {
            return state.cuentas
        },

        meta(state) {
            return state.meta
        },

        currentCuenta(state) {
            return state.currentCuenta
        }
    }
}