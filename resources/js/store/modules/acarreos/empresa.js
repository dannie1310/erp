const URI = '/api/acarreos/empresa/';

export default{
    namespaced: true,
    state: {
        empresas: [],
        currentEmpresa: null,
        meta: {}
    },

    mutations: {
        SET_EMPRESAS(state, data){
            state.empresas = data
        },

        SET_META(state, data){
            state.meta = data
        },

        SET_EMPRESA(state, data){
            state.currentEmpresa = data
        },

        UPDATE_ATTRIBUTE(state, data) {
            _.set(state.currentEmpresa, data.attribute, data.value);
        },

        UPDATE_EMPRESA(state, data) {
            state.empresas = state.empresas.map(e => {
                if (e.id === data.id) {
                    return Object.assign({}, e, data)
                }
                return e
            })
            state.currentEmpresa = data;
        },
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
                    });
            });
        },
        update(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Actualizar la empresa",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Actualizar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .patch(URI + payload.id, payload.data,{ params: payload.params } )
                                .then(r => r.data)
                                .then(data => {
                                    swal("Empresa actualizada correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    })
                                        .then(() => {
                                            resolve(data);
                                        })
                                })
                                .catch(error => {
                                    reject(error);
                                })
                        }
                    });
            });
        },
    },

    getters: {
        empresas(state) {
            return state.empresas
        },

        meta(state) {
            return state.meta
        },

        currentEmpresa(state) {
            return state.currentEmpresa
        }
    }
}
