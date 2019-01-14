const URL = '/api/contabilidad/cuenta-fondo/'

export default {
    namespaced:true,
    state:{
        cuentas: [],
        meta: {}
    },
    mutations:{
        fetch(state,data){
            state.cuentas = data;
        },
        setMeta(state,data){
            state.meta = data;
        },
        update(state, payload) {
            state.cuentas = state.cuentas.map(cuenta => {
                if (cuenta.id === payload.id) {
                    return Object.assign([], cuenta, payload.data)
                }
                return cuenta
            })
        }
    },
    actions:{
        paginate(context, params){
            axios.get(URL + "paginate",{params:params})
                .then(response => {
                    context.commit('fetch',response.data.data);
                    context.commit('setMeta',response.data.meta);
                })
        },

        find(context, id) {
            return new Promise((resolve, reject) => {
                axios.get(URL + id)
                    .then(res => {
                        resolve(res.data)
                    })
                    .catch(err => {
                        reject(err)
                    })
            })
        },

        update(context, payload) {
            return new Promise((resolve, reject) => {
                axios.patch(URL + payload.id, payload)
                    .then(response => {
                        context.commit('update', {id: payload.id, data: response.data});
                        resolve(response.data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            })
        }

    },
    getters:{
        meta(state){
            return state.meta;
        },
        cuentas(state){
            return state.cuentas;
        }
    }
}