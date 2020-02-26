<template>
    <span>
        <button @click="find(id)" type="button" class="btn btn-sm btn-outline-danger" title="Rechazar">
            <i class="fa fa-times"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> RECHAZAR SOLICITUD DE BAJA DE CUENTA BANCARIA</h5>
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
                    <form role="form" @submit.prevent="validate">
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-11 form-group row error-content">
                                    <b>Motivo de Rechazo: </b>
                            </div>
                         </div>
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-11">
                                <div class="form-group row error-content">
                                    <div class="col-sm-11">
                                        <textarea
                                                name="observaciones"
                                                id="observaciones"
                                                class="form-control"
                                                v-model="observaciones"
                                                v-validate="{required: true}"
                                                data-vv-as="Observaciones"
                                                :class="{'is-invalid': errors.has('observaciones')}"
                                        ></textarea>
                                        <div class="invalid-feedback" v-show="errors.has('observaciones')">{{ errors.first('observaciones') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button @click="init(id)" type="button" class="btn btn-primary"><i class="fa fa-file-pdf-o"></i>  Ver Archivo Soporte</button>
                            <button type="submit" class="btn btn-danger">Rechazar</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
                    </form>
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
        name: "solicitud-baja-rechazar",
        props: ['id'],
        data() {
            return {
                observaciones: ''
            }
        },
        methods: {
            find(id) {
                this.$store.commit('finanzas/solicitud-alta-cuenta-bancaria/SET_CUENTA', null);
                return this.$store.dispatch('finanzas/solicitud-baja-cuenta-bancaria/find', {
                    id: id,
                    params: { include: ['moneda', 'subcontrato', 'tipo_cuenta', 'empresa','banco','tipo','plaza','movimientos','movimientos.usuario','movimiento_solicitud'] }
                }).then(data => {
                    this.$store.commit('finanzas/solicitud-baja-cuenta-bancaria/SET_CUENTA', data);
                    $(this.$refs.modal).draggable();
                    $(this.$refs.modal).modal('show');
                })
            },
            init() {
                this.pdf()
            },
            pdf(){
                var url = '/api/finanzas/gestion-cuenta-bancaria/solicitud-baja/pdf/' + this.id +'?db=' + this.$session.get('db') + '&idobra=' + this.$session.get('id_obra')+'&access_token='+this.$session.get('jwt');
                $(this.$refs.body).html('<iframe src="'+url+'"  frameborder="0" height="100%" width="100%">CONSULTA DE ARCHIVO DE SOPORTE SOLICITUD DE ALTA DE CUENTA BANCARIA</iframe>');
                $(this.$refs.modalPDF).draggable();
                $(this.$refs.modalPDF).modal('show');
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.rechazar()
                    }
                });
            },
            rechazar() {
                return this.$store.dispatch('finanzas/solicitud-baja-cuenta-bancaria/rechazar', {
                    id: this.id,
                    params: { include: ['moneda', 'subcontrato','empresa','banco','tipo','plaza','mov_estado','movimientos.usuario','movimiento_solicitud'], data:[this.$data.observaciones]}
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

<style scoped>

</style>