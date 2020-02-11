const URI = '/api/subcontratos-estimaciones/retencion-liberacion/';
export default{
    namespaced: true,
    state: {
        liberaciones: [],
        currentLiberacion: null,
        meta: {}
    },
    mutations: {
        SET_LIBERACIONES(state, data){
            state.liberaciones = data
        },

        SET_META(state, data){
            state.meta = data
        },

        SET_LIBERACION(state, data){
            state.currentLiberacion = data
        },
    },
    actions: {
        find(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                .get(URI + payload.id, { params: payload.params })
                .then(r => r.data)
                .then(data => {
                    resolve(data);
                })
                .catch(error => {
                    reject(error);
                })
            });
        },
        listLiberaciones(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id + '/listLiberaciones', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    });
            });
        },
        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Liberación",
                    text: "¿Está seguro de que la información es correcta?",
                    icon: "info",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Registrar',
                            closeModal: false,
                        }
                    }
                })
                .then((value) => {
                    if (value) {
                        axios
                        .post(URI, payload)
                        .then(r => r.data)
                        .then(data => {
                            swal("Liberación registrada correctamente", {
                                icon: "success",
                                timer: 2000,
                                buttons: false
                            }).then(() => {
                                resolve(data);
                            })
                        })
                        .catch(error => {
                            reject(error);
                        });
                    }
                });
            });
        },
    },
    getters: {
        liberaciones(state) {
            return state.liberaciones
        },

        meta(state) {
            return state.meta
        },

        currentLiberacion(state) {
            return state.currentLiberacion
        }
    }
}