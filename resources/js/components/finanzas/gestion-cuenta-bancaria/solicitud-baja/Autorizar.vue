<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-success" title="Autorizar">
            <i class="fa fa-check"></i>
        </button>
         <div class="modal fade" ref="modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> AUTORIZAR SOLICITUD DE BAJA DE CUENTA BANCARIA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                 <div class="modal-body">
                            <div class="row" v-if="solicitudBaja">
                                <div class="col-12">
                                    <div class="invoice p-3 mb-3">
                                        <div class="row">
                                            <div class="table-responsive col-12">
                                                <table class="table table-striped">
                                                    <tbody>
                                                        <tr>
                                                            <th>Beneficiario:</th>
                                                            <td>{{solicitudBaja.empresa.razon_social}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Cuenta / CLABE / Núm. Tarjeta:</th>
                                                            <td>{{solicitudBaja.cuenta}}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row"  v-if="solicitudBaja">
                                <div class="col-12">
                                    <div class="invoice p-3 mb-3">
                                        <div class="row">
                                            <div class="col-12">
                                                <h5>Datos de la Cuenta</h5>
                                            </div>
                                        </div>
                                        <form role="form">
                                            <div class="row">
                                                <div class="table-responsive col-md-12">
                                                    <table class="table table-striped">
                                                        <tbody>
                                                            <tr>
                                                                <td><b>Banco:</b></td>
                                                                <td>{{solicitudBaja.banco.razon_social}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><b>Modena:</b></td>
                                                                <td>{{solicitudBaja.moneda.nombre}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><b>Sucursal:</b></td>
                                                                <td>{{solicitudBaja.sucursal_format}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><b>Plaza:</b></td>
                                                                <td>{{solicitudBaja.plaza.clave_format}} ({{solicitudBaja.plaza.nombre}})</td>
                                                            </tr>
                                                            <tr>
                                                                <td><b>Tipo:</b></td>
                                                                <td>{{solicitudBaja.tipo_cuenta.descripcion}}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                             </div>
                             <div class="row" v-if="solicitudBaja">
                                <div class="col-12">
                                    <div class = "col-sm-6">
                                        <label>Observaciones:</label>
                                    </div>
                                    <div class = "col-sm-6">
                                          <h6>{{solicitudBaja.observaciones}}</h6>
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button @click="pdf()" type="button" class="btn btn-primary"><i class="fa fa-file-pdf-o"></i>  Ver Archivo Soporte</button>
                         <button @click="autorizar()" type="button" class="btn btn-success">Autorizar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" ref="modalPDF" tabindex="-1" role="dialog" aria-labelledby="PDFModal" style="overflow: hidden;">
             <div class="modal-dialog modal-lg" id="mdialTamanio">
                 <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"> CONSULTA DE ARCHIVO DE SOPORTE SOLICITUD DE BAJA DE CUENTA BANCARIA</h4>
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
                    </div>
                    <div class="modal-body modal-lg" style="height: 800px" ref="body">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    </div>
                 </div>
             </div>
         </div>
    </span>
</template>

<script>
    export default {
        name: "solicitud-baja-autorizar",
        props: ['id'],
        methods: {
            find() {
                this.$store.commit('finanzas/solicitud-baja-cuenta-bancaria/SET_CUENTA', null);
                return this.$store.dispatch('finanzas/solicitud-baja-cuenta-bancaria/find', {
                    id: this.id,
                    params: {include: ['moneda', 'subcontrato', 'tipo_cuenta', 'empresa', 'banco', 'tipo', 'plaza', 'movimientos.usuario', 'mov_estado']}
                }).then(data => {
                    this.$store.commit('finanzas/solicitud-baja-cuenta-bancaria/SET_CUENTA', data);
                    $(this.$refs.modal).draggable();
                    $(this.$refs.modal).appendTo('body')
                    $(this.$refs.modal).modal('show');
                })
            },
            pdf(){
                var url = '/api/finanzas/gestion-cuenta-bancaria/solicitud-baja/pdf/' + this.id +'?db=' + this.$session.get('db') + '&idobra=' + this.$session.get('id_obra')+'&access_token='+this.$session.get('jwt');
                $(this.$refs.body).html('<iframe src="'+url+'"  frameborder="0" height="100%" width="100%">CONSULTA DE ARCHIVO DE SOPORTE SOLICITUD DE BAJA DE CUENTA BANCARIA</iframe>');
                $(this.$refs.modalPDF).draggable();
                $(this.$refs.modalPDF).appendTo('body')
                $(this.$refs.modalPDF).modal('show');
            },
            autorizar() {
                return this.$store.dispatch('finanzas/solicitud-baja-cuenta-bancaria/autorizar', {
                    id: this.id,
                    params: { include: ['moneda', 'subcontrato','empresa','banco','tipo','plaza','movimientos','movimientos.usuario','mov_estado'] }
                }).then(data => {
                    this.$store.commit('finanzas/solicitud-baja-cuenta-bancaria/UPDATE_CUENTA', data)
                    $(this.$refs.modal).modal('hide');
                })
                    .finally( ()=>{
                        this.cargando = false;
                    });
            }
        },
        computed: {
            solicitudBaja() {
                return this.$store.getters['finanzas/solicitud-baja-cuenta-bancaria/currentCuenta'];
            }
        }
    }
</script>
