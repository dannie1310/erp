const URI = '/api/fiscal/cfd-sat/';
export default {
    namespaced: true,
    state: {
        CFDSAT: [],
        currentCFDSAT: null,
        meta: {},
    },

    mutations: {
        SET_CFDSAT(state, data) {
            state.CFDSAT = data;
        },

        SET_META(state, data) {
            state.meta = data;
        },

        SET_cCFDSAT(state, data) {
            state.currentCFDSAT = data;
        },
        UPDATE_CFDSAT(state, data) {
            state.CFDSAT = state.CFDSAT.map(cfd => {
                if (cfd.id === data.id) {
                    return Object.assign({}, cfd, data)
                }
                return cfd
            })
            state.currentCFDSAT = state.currentCFDSAT ? data : null;
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
        paginate (context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'paginate', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        context.commit("SET_CFDSAT", data.data);
                        context.commit("SET_META", data.meta);
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
        cargarXMLProveedor(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'cargar-xml-proveedor', payload.data, payload.config)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        descargarIndividual(context, payload) {

            var urr = URI + payload.id +  '/descargar?access_token=' + this._vm.$session.get('jwt');

            var win = window.open(urr, "_blank");

            win.onbeforeunload = () => {
                swal("CFDI descargado correctamente.", {
                    icon: "success",
                    timer: 2000,
                    buttons: false
                })
            }
        },

        descargar(context, payload){
            let filtros = 0;
            var search = '?';
            if (typeof payload.params.rfc_emisor !== 'undefined') {
                search = search + 'rfc_emisor='+ payload.params.rfc_emisor + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.rfc_receptor !== 'undefined') {
                search = search + 'rfc_receptor='+ payload.params.rfc_receptor + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.emisor !== 'undefined') {
                search = search + 'emisor='+ payload.params.emisor + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.receptor !== 'undefined') {
                search = search + 'receptor='+ payload.params.receptor + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.startDate !== 'undefined') {
                search = search + 'startDate='+ payload.params.startDate + '&';
                filtros = +filtros + 1;
            }

            if (typeof payload.params.endDate !== 'undefined') {
                search = search + 'endDate='+ payload.params.endDate + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.fecha !== 'undefined') {
                search = search + 'fecha='+ payload.params.fecha + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.uuid !== 'undefined') {
                search = search + 'uuid='+ payload.params.uuid + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.subtotal !== 'undefined') {
                search = search + 'subtotal='+ payload.params.subtotal + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.total !== 'undefined') {
                search = search + 'total='+ payload.params.total + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.descuento !== 'undefined') {
                search = search + 'descuento='+ payload.params.descuento + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.moneda !== 'undefined') {
                search = search + 'moneda='+ payload.params.moneda + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.tipo_cambio !== 'undefined') {
                search = search + 'tipo_cambio='+ payload.params.tipo_cambio + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.serie !== 'undefined') {
                search = search + 'serie='+ payload.params.serie + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.folio !== 'undefined') {
                search = search + 'folio='+ payload.params.folio + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.impuestos_retenidos !== 'undefined') {
                search = search + 'impuestos_retenidos='+ payload.params.impuestos_retenidos + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.impuestos_trasladados !== 'undefined') {
                search = search + 'impuestos_trasladados='+ payload.params.impuestos_trasladados + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.estado !== 'undefined') {
                search = search + 'estado='+ payload.params.estado + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.tipo_comprobante !== 'undefined') {
                search = search + 'tipo_comprobante='+ payload.params.tipo_comprobante + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.base_datos !== 'undefined') {
                search = search + 'base_datos='+ payload.params.obra + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.obra !== 'undefined') {
                search = search + 'obra='+ payload.params.obra + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.solo_pendientes !== 'undefined') {
                search = search + 'solo_pendientes='+ payload.params.solo_pendientes + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.solo_asociados !== 'undefined') {
                search = search + 'solo_asociados='+ payload.params.solo_asociados + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.solo_asociados_contabilidad !== 'undefined') {
                search = search + 'solo_asociados_contabilidad='+ payload.params.solo_asociados_contabilidad + '&';
                filtros = +filtros + 1;
            }

            if (typeof payload.params.base_datos_ctpq !== 'undefined') {
                search = search + 'base_datos_ctpq='+ payload.params.base_datos_ctpq + '&';
                filtros = +filtros + 1;
            }

            if (typeof payload.params.ejercicio !== 'undefined') {
                search = search + 'ejercicio='+ payload.params.ejercicio + '&';
                filtros = +filtros + 1;
            }

            if (typeof payload.params.periodo !== 'undefined') {
                search = search + 'periodo='+ payload.params.periodo + '&';
                filtros = +filtros + 1;
            }

            if (typeof payload.params.tipo_poliza !== 'undefined') {
                search = search + 'tipo_poliza='+ payload.params.tipo_poliza + '&';
                filtros = +filtros + 1;
            }

            if (typeof payload.params.folio_poliza !== 'undefined') {
                search = search + 'folio_poliza='+ payload.params.folio_poliza + '&';
                filtros = +filtros + 1;
            }

            if (typeof payload.params.fecha_poliza !== 'undefined') {
                search = search + 'folio_poliza='+ payload.params.fecha_poliza + '&';
                filtros = +filtros + 1;
            }

            if(filtros == 0){
                swal({
                    title: "Aviso",
                    text: "Debe utilizar al menos un filtro de búsqueda",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cerrar',
                            visible: true
                        }
                    }
                })
            }else{
                var urr = URI +  'descargar'+ search+'access_token=' + this._vm.$session.get('jwt');

                var win = window.open(urr, "_blank");

                win.onbeforeunload = () => {
                    swal("CFDI descargados correctamente.", {
                        icon: "success",
                        timer: 2000,
                        buttons: false
                    })
                }
            }


        },

        descargarComunicados(context, payload){
            let filtros = 0;
            var search = '?';
            if (typeof payload.params.scope !== 'undefined') {
                search = search + 'scope='+payload.params.scope+'&';
            }
            if (typeof payload.params.rfc_emisor !== 'undefined') {
                search = search + 'rfc_emisor='+ payload.params.rfc_emisor + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.rfc_receptor !== 'undefined') {
                search = search + 'rfc_receptor='+ payload.params.rfc_receptor + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.emisor !== 'undefined') {
                search = search + 'emisor='+ payload.params.emisor + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.receptor !== 'undefined') {
                search = search + 'receptor='+ payload.params.receptor + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.startDate !== 'undefined') {
                search = search + 'startDate='+ payload.params.startDate + '&';
                filtros = +filtros + 1;
            }

            if (typeof payload.params.endDate !== 'undefined') {
                search = search + 'endDate='+ payload.params.endDate + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.fecha !== 'undefined') {
                search = search + 'fecha='+ payload.params.fecha + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.uuid !== 'undefined') {
                search = search + 'uuid='+ payload.params.uuid + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.subtotal !== 'undefined') {
                search = search + 'subtotal='+ payload.params.subtotal + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.total !== 'undefined') {
                search = search + 'total='+ payload.params.total + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.descuento !== 'undefined') {
                search = search + 'descuento='+ payload.params.descuento + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.moneda !== 'undefined') {
                search = search + 'moneda='+ payload.params.moneda + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.tipo_cambio !== 'undefined') {
                search = search + 'tipo_cambio='+ payload.params.tipo_cambio + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.serie !== 'undefined') {
                search = search + 'serie='+ payload.params.serie + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.folio !== 'undefined') {
                search = search + 'folio='+ payload.params.folio + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.impuestos_retenidos !== 'undefined') {
                search = search + 'impuestos_retenidos='+ payload.params.impuestos_retenidos + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.impuestos_trasladados !== 'undefined') {
                search = search + 'impuestos_trasladados='+ payload.params.impuestos_trasladados + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.estado !== 'undefined') {
                search = search + 'estado='+ payload.params.estado + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.tipo_comprobante !== 'undefined') {
                search = search + 'tipo_comprobante='+ payload.params.tipo_comprobante + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.base_datos !== 'undefined') {
                search = search + 'base_datos='+ payload.params.obra + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.obra !== 'undefined') {
                search = search + 'obra='+ payload.params.obra + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.solo_pendientes !== 'undefined') {
                search = search + 'solo_pendientes='+ payload.params.solo_pendientes + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.solo_asociados !== 'undefined') {
                search = search + 'solo_asociados='+ payload.params.solo_asociados + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.solo_asociados_contabilidad !== 'undefined') {
                search = search + 'solo_asociados_contabilidad='+ payload.params.solo_asociados_contabilidad + '&';
                filtros = +filtros + 1;
            }

            if (typeof payload.params.base_datos_ctpq !== 'undefined') {
                search = search + 'base_datos_ctpq='+ payload.params.base_datos_ctpq + '&';
                filtros = +filtros + 1;
            }

            if (typeof payload.params.ejercicio !== 'undefined') {
                search = search + 'ejercicio='+ payload.params.ejercicio + '&';
                filtros = +filtros + 1;
            }

            if (typeof payload.params.periodo !== 'undefined') {
                search = search + 'periodo='+ payload.params.periodo + '&';
                filtros = +filtros + 1;
            }

            if (typeof payload.params.tipo_poliza !== 'undefined') {
                search = search + 'tipo_poliza='+ payload.params.tipo_poliza + '&';
                filtros = +filtros + 1;
            }

            if (typeof payload.params.folio_poliza !== 'undefined') {
                search = search + 'folio_poliza='+ payload.params.folio_poliza + '&';
                filtros = +filtros + 1;
            }

            if (typeof payload.params.fecha_poliza !== 'undefined') {
                search = search + 'folio_poliza='+ payload.params.fecha_poliza + '&';
                filtros = +filtros + 1;
            }

            var urr = URI +  'descargar-comunicados'+ search+'access_token=' + this._vm.$session.get('jwt');

            var win = window.open(urr, "_blank");

            win.onbeforeunload = () => {
                swal("Comunicados descargados correctamente.", {
                    icon: "success",
                    timer: 2000,
                    buttons: false
                })
            }

        },

        update(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Está seguro?",
                    text: "Guardar cambios del CFD",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Guardar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .patch(URI + payload.id, payload.data, { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("CFD Actualizado Correctamente", {
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
        cargarZIP(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'carga-zip', payload.data, payload.config)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        procesaDirZIPCFD(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'procesa-dir-zip-cfd', payload.data, payload.config)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        obtenerInformeCFDEmpresaMes(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'obtener-informe-empresa-mes', payload)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        obtenerInformeCFDICompleto(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'obtener-informe-completo', payload)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        getContenidoDirectorio(context, payload){
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'obtener-contenido-directorio', payload)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        obtenerInformeSATLP2020(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'obtener-informe-sat-lp-2020', payload)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },

        obtenerCuentasInformeSATLP2020(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI  + payload.id + '/obtener-cuentas-informe-sat-lp-2020', payload)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },

        obtenerMovimientosCuentasInformeSATLP2020(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI  + payload.id_cuenta + '/obtener-movimientos-cuentas-informe-sat-lp-2020', payload)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },

        getListaCFDI(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI  + payload.id_proveedor_sat + '/obtener-lista-cfdi', payload)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },

        getListaCFDIMesAnio(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI  + payload.empresa_sat + '/obtener-lista-cfdi-mes-anio', payload)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },

        getListaCFDICostosCFDIBalanza(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI  + payload.empresa_sat + '/obtener-lista-cfdi-costos-cfdi-costos-balanza', payload)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },

        getNumeroEmpresaContexto(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI  + 'obtener-numero-empresa', payload)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },

        obtenerInformeCostosCFDIvsCostosBalanza(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(URI + 'obtener-informe-costos-cfdi-vs-costos-balanza', payload)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },

        descargaLayout(context, payload){
            let filtros = 0;
            var search = '?';
            if (typeof payload.params.scope !== 'undefined') {
                search = search + 'scope='+payload.params.scope+'&';
            }
            if (typeof payload.params.rfc_emisor !== 'undefined') {
                search = search + 'rfc_emisor='+ payload.params.rfc_emisor + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.rfc_receptor !== 'undefined') {
                search = search + 'rfc_receptor='+ payload.params.rfc_receptor + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.emisor !== 'undefined') {
                search = search + 'emisor='+ payload.params.emisor + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.receptor !== 'undefined') {
                search = search + 'receptor='+ payload.params.receptor + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.startDate !== 'undefined') {
                search = search + 'startDate='+ payload.params.startDate + '&';
                filtros = +filtros + 1;
            }

            if (typeof payload.params.endDate !== 'undefined') {
                search = search + 'endDate='+ payload.params.endDate + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.fecha !== 'undefined') {
                search = search + 'fecha='+ payload.params.fecha + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.uuid !== 'undefined') {
                search = search + 'uuid='+ payload.params.uuid + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.subtotal !== 'undefined') {
                search = search + 'subtotal='+ payload.params.subtotal + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.total !== 'undefined') {
                search = search + 'total='+ payload.params.total + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.descuento !== 'undefined') {
                search = search + 'descuento='+ payload.params.descuento + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.moneda !== 'undefined') {
                search = search + 'moneda='+ payload.params.moneda + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.tipo_cambio !== 'undefined') {
                search = search + 'tipo_cambio='+ payload.params.tipo_cambio + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.serie !== 'undefined') {
                search = search + 'serie='+ payload.params.serie + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.folio !== 'undefined') {
                search = search + 'folio='+ payload.params.folio + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.impuestos_retenidos !== 'undefined') {
                search = search + 'impuestos_retenidos='+ payload.params.impuestos_retenidos + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.impuestos_trasladados !== 'undefined') {
                search = search + 'impuestos_trasladados='+ payload.params.impuestos_trasladados + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.estado !== 'undefined') {
                search = search + 'estado='+ payload.params.estado + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.tipo_comprobante !== 'undefined') {
                search = search + 'tipo_comprobante='+ payload.params.tipo_comprobante + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.base_datos !== 'undefined') {
                search = search + 'base_datos='+ payload.params.obra + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.obra !== 'undefined') {
                search = search + 'obra='+ payload.params.obra + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.solo_pendientes !== 'undefined') {
                search = search + 'solo_pendientes='+ payload.params.solo_pendientes + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.solo_asociados !== 'undefined') {
                search = search + 'solo_asociados='+ payload.params.solo_asociados + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.solo_asociados_contabilidad !== 'undefined') {
                search = search + 'solo_asociados_contabilidad='+ payload.params.solo_asociados_contabilidad + '&';
                filtros = +filtros + 1;
            }
            if (typeof payload.params.solo_no_asociados_contabilidad !== 'undefined') {
                search = search + 'solo_no_asociados_contabilidad='+ payload.params.solo_no_asociados_contabilidad + '&';
                filtros = +filtros + 1;
            }
            //

            if(filtros == 0){
                swal({
                    title: "Aviso",
                    text: "Debe utilizar al menos un filtro de búsqueda",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cerrar',
                            visible: true
                        }
                    }
                })
            }else {
                var urr = URI + 'descargaLayout'+ search+'&db=' + this._vm.$session.get('db') + '&idobra=' + this._vm.$session.get('id_obra') + '&access_token=' + this._vm.$session.get('jwt');
                var win = window.open(urr, "_blank");

                win.onbeforeunload = () => {
                    swal("Layout descargado correctamente.", {
                        icon: "success",
                        timer: 2000,
                        buttons: false
                    })
                }
            }
        },

    },

    getters: {
        CFDSAT(state) {
            return state.CFDSAT
        },

        meta(state) {
            return state.meta
        },

        currentCFDSAT(state) {
            return state.currentCFDSAT
        }
    }
}
