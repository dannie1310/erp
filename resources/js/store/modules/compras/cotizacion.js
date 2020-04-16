const URI = '/api/compras/cotizacion/';

export default {
    namespaced: true,
    state: {
        cotizaciones: [],
        meta: {}
    },

    mutations: {
        SET_COTIZACIONES(state, data) {
            state.cotizaciones = data
        },

        SET_META(state, data) {
            state.meta = data;
        }
    },

    actions: {
        paginate (context, payload) {
            console.log('paginate cotizaciones', payload);
            
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
    },

    getters: {
        cotizaciones(state) {
            return state.cotizaciones
        },

        meta(state) {
            return state.meta
        }
    }
}
