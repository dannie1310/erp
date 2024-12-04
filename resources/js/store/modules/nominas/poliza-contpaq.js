const URI = '/api/nominas/poliza/';
export default {
    namespaced: true,
    state: {
        polizas: [],
        currentPoliza: null,
        meta: {},
    },

    mutations: {
        SET_POLIZAS(state, data) {
            state.polizas = data;
        },

        SET_META(state, data) {
            state.meta = data;
        },

        UPDATE_ATTRIBUTE(state, data) {
            _.set(state.currentPoliza, data.attribute, data.value);
        },

        UPDATE_POLIZA(state, data) {
            state.polizas = state.polizas.map(poliza => {
                if (poliza.id === data.id) {
                    return Object.assign({}, poliza, data)
                }
                return poliza
            })
            state.currentPoliza = state.currentPoliza ? data : null;
        },

        SET_POLIZA(state, data) {
            state.currentPoliza = data;
        }
    },

    actions: {
        index(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    });
            });
        },

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
        descargaXML(context, payload){
            var urr = URI + 'descargaXML?id=' + payload.id + '&empresa=' + payload.id_empresa +'&access_token=' + this._vm.$session.get('jwt');
            var win = window.open(urr, "_blank");
            win.onbeforeunload = () => {
                swal("XML descargado correctamente.", {
                    icon: "success",
                    timer: 2000,
                    buttons: false
                })
            }
        },
        correo(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Envio de correo",
                    text: "¿Está seguro de que desea enviar el correo?",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Enviar',
                            closeModal: false,
                        }
                    },
                    dangerMode: true,
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .get(URI + payload.id+'/correo', { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Correo enviado correctamente", {
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
        polizas(state) {
            return state.polizas
        },

        meta(state) {
            return state.meta
        },

        currentPoliza(state) {
            return state.currentPoliza
        }
    }
}
