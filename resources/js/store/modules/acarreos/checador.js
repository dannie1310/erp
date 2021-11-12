const URI = '/api/acarreos/checador/';

export default {
    namespaced: true,
    state: {
        checadores: [],
        currentChecador: '',
        meta:{}
    },

    mutations: {
        SET_CHECADORES(state, data) {
            state.checadores = data;
        },
        SET_CHECADOR(state, data) {
            state.currentChecador = data;
        },
        SET_META(state, data) {
            state.meta = data;
        },
        UPDATE_ATTRIBUTE(state, data) {
            _.set(state.currentChecador, data.attribute, data.value);
        },
    },

    actions: {
        getChecadores(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'getChecadores', { params: payload.params })
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
        checadores(state) {
            return state.checadores
        },
        currentChecador(state) {
            return state.currentChecador
        },
        meta(state) {
            return state.meta;
        },
    }
}