const URI = '/api/contabilidad/poliza/';
export default {
    namespaced: true,
    state: {
        polizas: [],
        currentPoliza: {},
        meta: {},
        cargando: true
    },

    mutations: {
        SET_POLIZAS(state, data) {
            state.polizas = data;
        },

        SET_META(state, data) {
            state.meta = data;
        },

        SET_CARGANDO(state, data) {
            state.cargando = data
        },

        UPDATE_POLIZA(state, data) {
            state.polizas = state.polizas.map(poliza => {
                if (poliza.id === payload.id) {
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
            context.commit('SET_CARGANDO', true);
            axios.get(URI + 'paginate', {params: payload})
                .then(r => r.data)
                .then((data) => {
                    context.commit('SET_POLIZAS', data.data)
                    context.commit('SET_META', data.meta)
                    context.commit('SET_CARGANDO', false);
                })
        },

        find(context, payload) {
            context.commit('SET_CARGANDO', true);
            axios.get(URI + payload.id, {params: payload.params})
                .then(r => r.data)
                .then((data) => {
                    context.commit('SET_POLIZA', data)
                    context.commit('SET_CARGANDO', false);
                })
        },

        update(context, payload) {
            context.commit('SET_CARGANDO', true);
            axios.patch(URI + payload.id, payload.data, {params: payload.params})
                .then(r => r.data)
                .then((data) => {
                    context.commit('UPDATE_POLIZA', data);
                    context.commit('SET_CARGANDO', false);
                })
        },

        validar(context, id) {
            context.commit('SET_CARGANDO', true);
            axios.patch(URI + id + '/validar')
                .then(r => r.data)
                .then((data) => {
                    context.commit('UPDATE_POLIZA', data.data)
                    context.commit('SET_CARGANDO', false)
                })
        }
    },

    getters: {
        polizas(state) {
            return state.polizas
        },

        meta(state) {
            return state.meta
        },

        cargando(state) {
            return state.cargando
        },

        currentPoliza(state) {
            return state.currentPoliza
        }
    }
}