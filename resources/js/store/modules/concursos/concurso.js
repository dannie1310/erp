const URI = '/api/concursos/concurso/';
export default{
    namespaced: true,
    state: {
        concursos: [],
        currentConcurso: null,
        meta: {}
    },

    mutations: {
        SET_CONCURSOS(state, data){
            state.concursos = data
        },

        SET_META(state, data){
            state.meta = data
        },

        SET_CONCURSO(state, data){
            state.currentConcurso = data
        },

        UPDATE_CONCURSOS(state, data) {
            state.concursos = state.concursos.map(concurso => {
                if (concurso.id === data.id) {
                    return Object.assign({}, concurso, data)
                }
                return concurso
            })
            state.currentConcurso != null ? data : null;
        }
    },

    actions: {
        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Concurso",
                    text: "¿Está seguro/a de que desea registrar el concurso?",
                    icon: "info",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Registrar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI, payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Concurso registrado correctamente", {
                                        icon: "success",
                                        timer: 2000,
                                        buttons: false
                                    }).then(() => {
                                        resolve(data);
                                    })
                                })
                                .catch(error => {
                                    reject(error);
                                });
                        }
                    });
            });
        },
    },

    getters: {
        concursos(state) {
            return state.concursos
        },

        meta(state) {
            return state.meta
        },

        currentConcurso(state) {
            return state.currentConcurso
        }
    }
}
