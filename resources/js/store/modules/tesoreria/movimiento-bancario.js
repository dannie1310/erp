const URI = '/api/tesoreria/movimiento-bancario/';

export default {
    namespaced: true,
    state: {
        movimientos: [],
        currentMovimiento: null,
        meta: {},
        cargando: true
    },

    mutations: {
        SET_MOVIMIENTOS(state, data) {
            state.movimientos = data
        },

        SET_MOVIMIENTO(state, data) {
            state.currentMovimiento = data
        },

        SET_META(state, data) {
            state.meta = data
        },

        SET_CARGANDO(state, data) {
            state.cargando = data
        },

        DELETE_MOVIMIENTO(state, id) {
            state.movimientos = state.movimientos.filter((mov) => {
                return mov.id !== id;
            })
            if (state.currentMovimiento && state.currentMovimiento.id === id) {
                state.currentMovimiento = null;
            }
        },
    },

    actions: {
        paginate (context, payload){
            axios.get(URI + 'paginate', {params: payload})
                .then(r => r.data)
                .then(data => {
                    context.commit('SET_MOVIMIENTOS', data.data)
                    context.commit('SET_META', data.meta)
                })
        },

        find(context, payload) {
            context.commit('SET_CARGANDO', true);
            axios.get(URI + payload.id, {params: payload.params})
                .then(r => r.data)
                .then((data) => {
                    context.commit('SET_MOVIMIENTO', data)
                    context.commit('SET_CARGANDO', false);
                })
        },

        delete(context, id) {
            context.commit('SET_CARGANDO', true);
            swal({
                title: "Eliminar movimiento",
                text: "¿Estás seguro/a de que deseas eliminar este movimiento?",
                icon: "warning",
                buttons: ['Cancelar', 'Si, Eliminar'],
                dangerMode: true,
            })
                .then((willDelete) => {
                    if (willDelete) {
                        axios.delete(URI + id)
                            .then(r => r.data)
                            .then(data => {
                                swal("Movimiento eliminado correctamente", {
                                    icon: "success",
                                }).then(() => {
                                    context.commit('DELETE_MOVIMIENTO', id)
                                    context.commit('SET_CARGANDO', false);
                                });
                            });
                    }
                });


        }
    },

    getters: {
        movimientos(state) {
            return state.movimientos
        },

        meta(state) {
            return state.meta
        },

        currentMovimiento(state) {
            return state.currentMovimiento
        }
    }
}