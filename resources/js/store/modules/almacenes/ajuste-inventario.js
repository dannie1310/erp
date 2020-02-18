const URI = '/api/almacenes/ajuste-inventario/';

export default{
    namespaced: true,
    state: {
        ajustes: [],
        currentAjuste: null,
        meta: {}
    },

    mutations: {
        SET_AJUSTES(state, data){
            state.ajustes = data
        },

        SET_META(state, data){
            state.meta = data
        },
        SET_AJUSTE(state, data){
            state.currentAjuste = data
        },
    },

    actions: {
        paginate(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'paginate', {params: payload.params})
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },

        cargaLayout(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Cargar Layout de Nuevo Lote de Ajuste de Inventario",
                    text: "¿Está seguro/a de que desea cargar partidas?",
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
                        if (value) {
                            axios
                                .post(URI + 'layout', payload.data, payload.config)
                                .then(r => r.data)
                                .then(data => {
                                        swal("Partidas agregadas correctamente:", {
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

        find (context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI+payload.id, {params: payload.params})
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        }
    },

    getters: {
        ajustes(state) {
            return state.ajustes
        },

        meta(state) {
            return state.meta
        },

        currentAjuste(state) {
            return state.currentAjuste
        }
    }
}
