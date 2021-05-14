const URI = '/api/fiscal/ctg-no-localizado/';
export default {
    namespaced: true,
    state: {
        ctg_no_localizados: [],
        currentCtgNoLocalizado: null,
        meta: {},
    },
    mutations: {
        SET_CTG_NO_LOCALIZADOS(state, data) {
            state.ctg_no_localizados = data;
        },
        SET_META(state, data) {
            state.meta = data;
        },
        SET_CTG_NO_LOCALIZADO(state, data) {
            state.currentCtgNoLocalizado = data;
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
                    title: "Cargar listado de contribuyentes no localizados",
                    text: "¿Está seguro de que desea cargar el archivo?",
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
                                    swal("Carga realizada correctamente.", {
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
        obtenerInforme(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'obtener-informe', payload)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
    },

    getters: {
        ctg_no_localizados(state) {
            return state.ctg_no_localizados
        },

        meta(state) {
            return state.meta
        },

        currentCtgNoLocalizado(state) {
            return state.currentCtgNoLocalizado
        }
    }
}
