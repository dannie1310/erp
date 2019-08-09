const URI = '/api/finanzas/gestion-cuenta-bancaria/solicitud-alta/';

export default{
    namespaced: true,
    state: {
        cuentas: [],
        currentCuenta: null,
        meta: {}
    },

    mutations: {
        SET_CUENTAS(state, data){
            state.cuentas = data
        },

        SET_META(state, data){
            state.meta = data
        },

        SET_CUENTA(state, data){
            state.currentCuenta = data
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

        autorizar(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'autorizar/'+payload.id, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        swal("Solicitud de alta de cuenta bancaria autorizada correctamente", {
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
            });
        },
    },

    getters: {
        cuentas(state) {
            return state.cuentas
        },

        meta(state) {
            return state.meta
        },

        currentCuenta(state) {
            return state.currentCuenta
        }
    }
}