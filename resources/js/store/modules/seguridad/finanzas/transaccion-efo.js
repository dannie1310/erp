const URI = '/api/SEGURIDAD_ERP/transaccion-efo/';


export default {
    namespaced: true,
    state: {
        transacciones: [],
        currenttransaccion: null,
        meta: {}
    },

    mutations: {
        SET_TRANSACCIONES(state, data) {
            state.transacciones = data
        },

        SET_TRANSACCION(state, data) {
            state.currenttransaccion = data;
        },

        SET_META(state, data) {
            state.meta = data
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
                        reject(error)
                    })
            });
        },

        index(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },

        paginate(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'paginate', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            })
        },

        descarga_csv(context, payload){
            var urr = URI + 'descarga-csv?db=' + '&access_token=' + this._vm.$session.get('jwt');
            var win = window.open(urr, "_blank");

            win.onbeforeunload = () => {
                swal("CSV descargado correctamente.", {
                    icon: "success",
                    timer: 2000,
                    buttons: false
                })
            }
        }
    },

    getters: {
        transacciones(state) {
            return state.transacciones
        },
        meta(state) {
            return state.meta
        }
    }
}
