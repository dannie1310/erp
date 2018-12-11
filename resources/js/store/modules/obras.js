export default {
    namespaced: true,

    state: {
        obras: []
    },
    mutations: {
        fetch(state, obras) {
            state.obras = obras;
        }
    },

    actions: {
        fetch (context){
            axios.get('/api/auth/obras')
                .then(res => {
                    context.commit('fetch', res.data)
                })
                .catch(err => {
                    alert(err);
                });
        }
    },

    getters: {
        obrasAgrupadas(state) {
            return _.groupBy(state.obras, 'base_datos');
        }
    }
}