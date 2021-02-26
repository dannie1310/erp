const URI = '/api/fiscal/no-localizado/';
export default {
    namespaced: true,
    state: {
        no_localizados: [],
        currentNoLocalizado: null,
        meta: {},
    },
    mutations: {
        SET_NO_LOCALIZADOS(state, data) {
            state.no_localizados = data;
        },
        SET_META(state, data) {
            state.meta = data;
        },
        SET_NO_LOCALIZADO(state, data) {
            state.currentNoLocalizado = data;
        },
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
        cargarCsv(context, payload){
            return new Promise((resolve, reject) => {
                swal({
                    title: "Cargar Csv Proveedores No Localizados",
                    text: "¿Está seguro/a de que desea cargar el archivo?",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Cargar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI + 'cargarCsv', payload.data, payload.config)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Carga registrada correctamente.", {
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
        no_localizados(state) {
            return state.no_localizados
        },

        meta(state) {
            return state.meta
        },

        currentNoLocalizado(state) {
            return state.currentNoLocalizado
        }
    }
}
