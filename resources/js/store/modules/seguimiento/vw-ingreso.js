const URI = '/api/seguimiento/vw-ingreso/';

export default {
    namespaced: true,
    state: {
        ingresos: [],
        currentIngreso: null,
        meta: {},
    },

    mutations: {
        SET_INGRESOS(state, data) {
            state.ingresos = data;
        },

        SET_INGRESO(state, data) {
            state.currentIngreso = data;
        },

        SET_META(state, data) {
            state.meta = data;
        },
    },

    actions: {
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
                    });
            });
        },
    },

    getters: {
        ingresos(state) {
            return state.ingresos;
        },
        meta(state) {
            return state.meta;
        },
        currentIngreso(state) {
            return state.currentIngreso;
        },
    }
}
