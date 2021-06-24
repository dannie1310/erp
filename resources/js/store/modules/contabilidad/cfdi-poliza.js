const URI = '/api/contabilidad/cfdi-poliza/';
export default {
    namespaced: true,
    state: {
        cfdis: [],
        currentCFDI: null,
        meta: {},
    },

    mutations: {
        SET_CFDIS(state, data) {
            state.cfdis = data;
        },

        SET_META(state, data) {
            state.meta = data;
        },

        UPDATE_CFDI(state, data) {
            state.cfdis = state.cfdis.map(cfdi => {
                if (cfdi.id === data.id) {
                    return Object.assign({}, cfdi, data)
                }
                return cfdi
            })
            state.currentCFDI = state.currentCFDI ? data : null;
        },

        SET_CFDI(state, data) {
            state.currentCFDI = data;
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

        getCFDIPorCargar(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI+'cfdi-por-cargar', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    });
            });
        },
        descargar(context, payload){
            var urr = URI +  'descargar-cfdi?db=' + this._vm.$session.get('db') + '&idobra=' + this._vm.$session.get('id_obra')+'&access_token=' + this._vm.$session.get('jwt');
            var win = window.open(urr, "_blank");

            win.onbeforeunload = () => {
                swal("CFDI descargados correctamente.", {
                    icon: "success",
                    timer: 2000,
                    buttons: false
                })
            }
        },
    },

    getters: {
        cfdis(state) {
            return state.cfdis
        },

        meta(state) {
            return state.meta
        },

        currentCFDI(state) {
            return state.currentCFDI
        }
    }
}
