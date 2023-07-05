const URI = '/api/contabilidad-general/layout-pasivo/';
export default {
    namespaced: true,
    state: {
        layouts: [],
        currentLayout: null,
        meta: {},
    },

    mutations: {
        SET_LAYOUTS(state, data) {
            state.layouts = data;
        },

        SET_META(state, data) {
            state.meta = data;
        },

        UPDATE_LAYOUT(state, data) {
            state.layouts = state.layouts.map(layout => {
                if (layout.id === data.id) {
                    return Object.assign({}, layout, data)
                }
                return layout
            })
            state.currentLayout = state.currentLayout ? data : null;
        },

        SET_LAYOUT(state, data) {
            state.currentLayout = data;
        }
    },

    actions: {
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
    },

    getters: {
        layouts(state) {
            return state.layouts
        },

        meta(state) {
            return state.meta
        },

        currentLayout(state) {
            return state.currentLayout
        }
    }
}
