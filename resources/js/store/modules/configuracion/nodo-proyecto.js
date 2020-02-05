const URI = '/api/configuracion/nodo-proyecto/';

export default {
    namespaced: true,
    state: {
        nodos: [],
        currentNodo: null,
        meta: {}
    },
    mutations: {
        SET_NODOS(state, data) {
            state.nodos = data
        },

        SET_NODO(state, data) {
            state.currentNodo = data;

        },
        SET_META(state, data){
            state.meta = data
        },
        UPDATE_NODO(state, data){
            state.nodos = state.nodos.map(nodo => {
                if(nodo.id === data.id){
                    return Object.assign({}, nodo, data)
                }
                return nodo
            })
            state.currentNodo = data ;
        },
        UPDATE_ATTRIBUTE(state, data) {
            state.currentNodo[data.attribute] = data.value
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
                        reject(error)
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
                        reject(error)
                    })
            });
        },

        asignaciones(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id + '/asignaciones', { params: payload.params })
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
    },

    getters: {
        nodos(state) {
            return state.nodos;
        },
        meta(state) {
            return state.meta;
        },
        currentNodo(state) {
            return state.currentNodo;
        }
    }

}
