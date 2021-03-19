const URI = '/api/catalogos/proyecto/';
export default {
    namespaced: true,
    state: {
        proyectos: [],
        currentProyecto: null,
        meta: {},
    },

    mutations: {
        SET_PROYECTOS(state, data) {
            state.proyectos = data;
        },

        SET_META(state, data) {
            state.meta = data;
        },

        UPDATE_PROYECTO(state, data) {
            state.proyectos = state.proyectos.map(proyecto => {
                if (proyecto.id === data.id) {
                    return Object.assign({}, proyecto, data)
                }
                return proyecto
            })
            state.currentProyecto = state.currentProyecto ? data : null;
        },

        SET_PROYECTO(state, data) {
            state.currentProyecto = data;
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
        },
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
    },

    getters: {
        proyectos(state) {
            return state.proyectos
        },

        meta(state) {
            return state.meta
        },

        currentProyecto(state) {
            return state.currentProyecto
        }
    }
}
