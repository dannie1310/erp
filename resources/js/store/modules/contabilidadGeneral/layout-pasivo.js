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
        cargaLayout(context, payload){
            return new Promise((resolve, reject) => {
                swal({
                    title: "Cargar Layout de Pasivos",
                    text: "¿Está seguro/a de que desea cargar xlsx?",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Agregar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        console.log(payload, payload.data.file, payload.config)
                        if (value) {
                            axios
                                .post(URI + 'cargalayout', payload.data, payload.config)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Archivo leido correctamente", {
                                        icon: "success",
                                        timer: 2000,
                                        buttons: false
                                    }).then(() => {
                                        resolve(data);
                                    })
                                })
                                .catch(error => {
                                    reject('Archivo no procesable');
                                })
                        }
                    });
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
