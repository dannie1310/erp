const URI = '/api/contratos/presupuesto/';

export default {
    namespaced: true,
    state: {
        presupuestos: [],
        currentPresupuesto: null,
        meta: {}
    },

    mutations: {
        SET_PRESUPUESTOS(state, data) {
            state.presupuestos = data
        },

        SET_META(state, data) {
            state.meta = data;
        },

        SET_PRESUPUESTO(state, data) {
            state.currentPresupuesto = data;
        }
    },

    actions: {
        paginate (context, payload) {
            console.log('Paginate presupuesto');
            

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
        }
    },

    getters: {
        presupuestos(state) {
            return state.presupuestos
        },

        meta(state) {
            return state.meta
        },

        currentPresupuesto(state) {
            return state.currentPresupuesto;
        }
    }
}
