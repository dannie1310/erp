const URI = '/api/almacenes/ajuste-inventario/';

export default{
    namespaced: true,
    state: {
        ajustes: [],
        currentAjuste: null,
        meta: {}
    },

    mutations: {
        SET_AJUSTES(state, data){
            state.ajustes = data
        },

        SET_META(state, data){
            state.meta = data
        },
        SET_AJUSTE(state, data){
            state.currentAjuste = data
        },
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

        find (context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI+payload.id, {params: payload.params})
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        }
    },

    getters: {
        ajustes(state) {
            return state.ajustes
        },

        meta(state) {
            return state.meta
        },

        currentAjuste(state) {
            return state.currentAjuste
        }
    }
}
