const URI = '/api/finanzas/carga-masiva/';

export default {
    namespaced: true,
    state: {
        layout: [],
        currentLayout: null,
        meta: {}
    },

    mutations: {
        SET_LAYOUTS(state, data) {
            state.layout = data
        },
        SET_LAYOUT(state, data) {
            state.currentLayout = data;
        },
        SET_META(state, data) {
            state.meta = data
        },
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
    },

    getters: {
        layouts(state) {
            return state.layout;
        },
        currentLayout(state) {
            return state.currentLayout;
        },
        meta(state) {
            return state.meta
        },
    }
}