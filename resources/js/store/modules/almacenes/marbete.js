const URI = '/api/almacenes/marbete/';
export default{
    namespaced: true,
    state: {
        marbetes: [],
        currentMarbete: null,
        meta: {}
    },

    mutations: {
        SET_MARBETES(state, data){
            state.marbetes = data
        },

        SET_META(state, data){
            state.meta = data
        },

        SET_MARBETE(state, data){
            state.currentMarbete = data
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
        }
    },

    getters: {
        marbetes(state) {
            return state.marbetes
        },

        meta(state) {
            return state.meta
        },

        currentMarbete(state) {
            return state.currentMarbete
        }
    }
}