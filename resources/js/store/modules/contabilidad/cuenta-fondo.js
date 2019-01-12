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
        }
    },
    actions:{
        paginate(context, params){
            axios.get(URL + "paginate",{params:params})
                .then(response => {
                    context.commit('fetch',response.data);
                    context.commit('setMeta',response.meta);
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