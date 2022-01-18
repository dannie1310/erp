const URI = '/api/padron-proveedores/invitacion/';

export default {
    namespaced: true,
    state: {
        invitaciones: [],
        currentInvitacion: null,
        meta: {}
    },

    mutations: {
        SET_INVITACIONES(state, data) {
            state.invitaciones = data;
        },

        SET_INVITACION(state, data) {
            state.currentInvitacion = data;
        },

        SET_META(state, data) {
            state.meta = data;
        }
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
        getSolicitudes(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'getSolicitudes', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    });
            });
        },
        getSolicitud(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id+'/getSolicitud', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        getPresupuestoEdit(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id+'/getPresupuestoEdit', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        find(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        abrir(context, payload){

            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'abrir/'+payload.id,   payload.params )
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },
        index(context, payload) {
            return new Promise((resolve, reject) => {

                axios
                    .get(URI, { params: payload.params })
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data.data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        getTiposArchivo(context, payload){
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.params.id+ '/tipos-archivo', {params: payload.params})
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    });
            });
        },
        cargarArchivos(context, payload){
            return new Promise((resolve, reject) => {

                swal({
                    title: "Subir Archivos a Invitación",
                    text: "¿Está seguro de que la información es correcta?",
                    icon: "info",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Subir Archivos',
                            closeModal: false,
                        }
                    }
                }).then((value) => {
                    if (value) {
                        axios
                        .post(URI + payload.id+ '/cargar-archivos',  payload.data)
                        .then(r => r.data)
                        .then(data => {
                            if(Array.isArray(data)){
                                swal("Archivos Subidos Correctamente", {
                                    icon: "success",
                                    timer: 2000,
                                    buttons: false
                                }).then(() => {
                                    resolve(data);
                                })
                            }else{
                                swal("Error al subir archivo, por favor reporte el incidente a Soporte a Aplicaciones", {
                                    icon: "error",
                                    title:"Error",
                                    timer: 3000,
                                    buttons: false
                                })
                            }

                        })
                        .catch(error => {
                            reject(error)
                        })
                    }
                });
            });
        },
    },

    getters: {
        invitaciones(state) {
            return state.invitaciones;
        },

        meta(state) {
            return state.meta;
        },

        currentInvitacion(state) {
            return state.currentInvitacion;
        }
    }
}
