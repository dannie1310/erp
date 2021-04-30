const URI = '/api/fiscal/fecha-inhabil-sat/';
export default{
    namespaced: true,
    state: {
        fechas_inhabiles: [],
        currentFecha: null,
        meta: {}
    },
    mutations: {
        SET_FECHAS_INHABILES(state, data){
            state.fechas_inhabiles = data
        },

        SET_META(state, data){
            state.meta = data
        },

        SET_FECHA_INHABIL(state, data){
            state.currentFecha = data
        },

        DELETE_FECHA(state, id) {
            state.fechas_inhabiles = state.fechas_inhabiles.filter((t) => {
                return t.id !== id;
            })
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
        delete(context, id) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Eliminar Fecha Inhábil SAT",
                    text: "¿Está seguro de que deseas eliminar la fecha inhábil?",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Eliminar',
                            closeModal: false,
                        }
                    },
                    dangerMode: true,
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .delete(URI + id)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Fecha inhábil eliminada correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    }).then(() => {
                                        resolve(data);
                                    })
                                })
                                .catch(error => {
                                    reject(error);
                                })
                        }
                    });
            });
        },
    },
    getters: {
        fechas_inhabiles(state) {
            return state.fechas_inhabiles
        },

        meta(state) {
            return state.meta
        },

        currentFecha(state) {
            return state.currentFecha
        }
    }

}