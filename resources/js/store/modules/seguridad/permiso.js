const URI = '/api/SEGURIDAD_ERP/permiso/';

export default {
    namespaced: true,

    state: {
        permisos: [],
        meta: {}
    },

    mutations: {
        SET_PERMISOS(state, data) {
            state.permisos = data;
        },

        SET_META(state, data) {
            state.meta = data;
        }
    },

    actions: {
        index(context, payload = {}) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI, payload.config)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    });
            });
        },
        porCantidad(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'por-cantidad',{params: payload.params})
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    });
            });
        },
        porObra(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'por-obra/' + payload.id, {params: payload.params})
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    });
            });
        },
        porUsuarioAuditoria(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'por-usuario-auditoria/' + payload.id, {params: payload.params})
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    });
            });
        },
        porUsuario(context, id) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'por-usuario/' + id)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    });
            });
        },
        descargaListado(contest, payload){
            return new Promise((resolve, reject) => {
                axios

                    .get(URI +  'descarga_listado_permisos_obra/' + payload.id , { params: payload.params, responseType:'blob', })
                    .then(r => r.data)
                    .then(data => {

                        const url = window.URL.createObjectURL(new Blob([data],{ type: 'text/csv' }));
                        const link = document.createElement('a');
                        link.href = url;
                        link.setAttribute('download', 'Listado de Permisos por Obra-'+payload.id+'.xlsx');
                        document.body.appendChild(link);
                        link.click();
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },
        descargaListadoUsuario(contest, payload){
            return new Promise((resolve, reject) => {
                axios
                    .get(URI +  'descarga_listado_permisos_usuario/' + payload.id , { params: payload.params, responseType:'blob', })
                    .then(r => r.data)
                    .then(data => {
                        const url = window.URL.createObjectURL(new Blob([data],{ type: 'text/csv' }));
                        const link = document.createElement('a');
                        link.href = url;
                        link.setAttribute('download', 'Listado de Permisos por Usuario-'+payload.params.usuario.nombre+'('+payload.params.usuario.usuario+').xlsx');
                        document.body.appendChild(link);
                        link.click();
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },


    },

    getters: {
        permisos(state) {
            return state.permisos
        },

        meta(state) {
            return state.meta;
        }
    }
}
