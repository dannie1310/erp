const URI = '/api/contabilidad-general/poliza-cfdi/';
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
        polizasEgresoSinCFDIXls(context, payload){

            var search = '?';
            if (typeof payload.params.scope !== 'undefined') {
                search = search + 'scope='+payload.params.scope+'&';
            }
            if (typeof payload.params.startDate !== 'undefined') {
                search = search + 'startDate='+ payload.params.startDate + '&';
            }
            if (typeof payload.params.endDate !== 'undefined') {
                search = search + 'endDate='+ payload.params.endDate + '&';
            }
            if (typeof payload.params.base_datos_ctpq !== 'undefined') {
                search = search + 'base_datos_ctpq='+ payload.params.base_datos_ctpq + '&';
            }
            if (typeof payload.params.empresa_ctpq !== 'undefined') {
                search = search + 'empresa_ctpq='+ payload.params.empresa_ctpq + '&';
            }
            if (typeof payload.params.ejercicio !== 'undefined') {
                search = search + 'ejercicio='+ payload.params.ejercicio + '&';
            }
            if (typeof payload.params.periodo !== 'undefined') {
                search = search + 'periodo='+ payload.params.periodo + '&';
            }
            if (typeof payload.params.tipo_poliza !== 'undefined') {
                search = search + 'tipo_poliza='+ payload.params.tipo_poliza + '&';
            }
            if (typeof payload.params.folio_poliza !== 'undefined') {
                search = search + 'folio_poliza='+ payload.params.folio_poliza + '&';
            }
            if (typeof payload.params.fecha_poliza !== 'undefined') {
                search = search + 'fecha_poliza='+ payload.params.fecha_poliza + '&';
            }

            var urr = URI + 'egresos-sin-cfdi-xls'+ search + '&access_token=' + this._vm.$session.get('jwt');
            var win = window.open(urr, "_blank");

            win.onbeforeunload = () => {
                swal("Archivo descargado correctamente.", {
                    icon: "success",
                    timer: 2000,
                    buttons: false
                })
            }
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
