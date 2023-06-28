const URI = '/api/contabilidad-general/contabilidad-electronica/';

export default {
    namespaced: true,
    state: {
        cuentas: [],
        currentCuenta: null,
        meta: {}
    },

    mutations: {
        SET_CUENTAS(state, data) {
            state.cuentas = data;
        },
        SET_CUENTA(state, data) {
            state.currentCuenta = data;
        },
        SET_META(state, data) {
            state.meta = data;
        },
    },
    actions: {
        cargarXML(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'xml', payload.data, payload.config)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        descargar(context, payload){
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'descargar', payload.datos, payload.config)
                    .then(r => r.data)
                    .then(data => {
                        const url = window.URL.createObjectURL(new Blob([data],{ type: 'text/csv' }));
                        const link = document.createElement('a');
                        link.href = url;
                        link.setAttribute('download', 'Balanza_'+payload.datos.rfc+'.csv');
                        document.body.appendChild(link);
                        link.click();
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
           /* var urr = URI + 'descargar?&access_token=' + this._vm.$session.get('jwt')+'&partidas=' + Object.values(payload.datos.partidas);

            var win = window.open(urr, "_blank");

            win.onbeforeunload = () => {
                swal("Layout descargado correctamente.", {
                    icon: "success",
                    timer: 2000,
                    buttons: false
                })
            }*/
        },
    },
    getters: {
        cuentas(state) {
            return state.cuentas;
        },
        meta(state) {
            return state.meta;
        },
        currentCuenta(state) {
            return state.currentCuenta;
        },
    }
}
