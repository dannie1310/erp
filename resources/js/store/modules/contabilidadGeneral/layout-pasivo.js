const URI = '/api/contabilidad-general/layout-pasivo/';
export default {
    namespaced: true,
    state: {
        layouts: [],
        currentLayout: null,
        actualizando:false,
        meta: {},
    },

    mutations: {
        SET_LAYOUTS(state, data) {
            state.layouts = data;
        },

        SET_META(state, data) {
            state.meta = data;
        },

        SET_ACTUALIZANDO(state, data){
            state.actualizando = data
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

        findCFDI(context, payload) {
            return new Promise((resolve, reject) => {
                axios.post(URI + payload.id_pasivo + '/lista-cfdi-asociar', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },
        asociarCFDI(context, payload) {
            return new Promise((resolve, reject) => {
                axios.post(URI + payload.id + '/asociar-cfdi', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },
        descargaLayoutIFS(context, payload) {

            var urr = URI + payload.id + '/descargar-layout-ifs?access_token=' + this._vm.$session.get('jwt');
            var win = window.open(urr, "_blank");

            win.onbeforeunload = () => {
                swal("Layout descargado correctamente.", {
                    icon: "success",
                    timer: 2000,
                    buttons: false
                })
            }
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
        },

        actualizando(state) {
            return state.actualizando
        }
    }
}
