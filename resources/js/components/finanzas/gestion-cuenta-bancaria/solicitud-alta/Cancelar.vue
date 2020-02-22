<template>
    <span>
        <button @click="find(id)" type="button" class="btn btn-sm btn-outline-danger" title="Cancelar">
            <i class="fa fa-ban"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> CANCELAR SOLICITUD DE ALTA DE CUENTA BANCARIA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                        <div class="modal-body">
                            <div class="row" v-if="solicitudAlta">
                                <div class="col-12">
                                    <div class="invoice p-3 mb-3">
                                        <div class="row">
                                            <div class="table-responsive col-12">
                                                <table class="table table-striped">
                                                    <tbody>
                                                        <tr>
                                                            <th>Tipo de Beneficiario:</th>
                                                            <td>{{solicitudAlta.empresa.tipo}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Beneficiario:</th>
                                                            <td>{{solicitudAlta.empresa.razon_social}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Banco:</th>
                                                            <td>{{solicitudAlta.banco.razon_social}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Cuenta / CLABE / No Tarjeta:</th>
                                                            <td>{{solicitudAlta.cuenta}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Modena:</th>
                                                            <td>{{solicitudAlta.moneda.nombre}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Sucursal:</th>
                                                            <td>{{solicitudAlta.sucursal_format}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Plaza:</th>
                                                            <td>{{solicitudAlta.plaza.clave_format}} ({{solicitudAlta.plaza.nombre}})</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Tipo:</th>
                                                            <td>{{solicitudAlta.tipo_cuenta.descripcion}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Observaciones:</th>
                                                            <td>{{solicitudAlta.observaciones}}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-11 form-group row error-content">
                                    <b>Motivo de Cancelación: </b>
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
                            <button type="submit" class="btn btn-danger">Cancelar</button>
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
                        <h4 class="modal-title"> CONSULTA DE ARCHIVO DE SOPORTE SOLICITUD DE ALTA DE CUENTA BANCARIA</h4>
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
        name: "solicitud-alta-cancelar",
        props: ['id'],
        data() {
            return {
                observaciones: ''
            }
        },
        methods: {
            find(id) {
                this.$store.commit('finanzas/solicitud-alta-cuenta-bancaria/SET_CUENTA', null);
                return this.$store.dispatch('finanzas/solicitud-alta-cuenta-bancaria/find', {
                    id: id,
                    params: { include: ['moneda', 'subcontrato', 'tipo_cuenta', 'empresa','banco','tipo','plaza','movimientos','movimientos.usuario','movimiento_solicitud'] }
                }).then(data => {
                    this.$store.commit('finanzas/solicitud-alta-cuenta-bancaria/SET_CUENTA', data);
                    $(this.$refs.modal).draggable();
                    $(this.$refs.modal).modal('show');
                })
            },
            init() {
                this.pdf()
            },
            pdf(){
                var url = '/api/finanzas/gestion-cuenta-bancaria/solicitud-alta/pdf/' + this.id +'?db=' + this.$session.get('db') + '&idobra=' + this.$session.get('id_obra')+'&access_token='+this.$session.get('jwt');
                $(this.$refs.body).html('<iframe src="'+url+'"  frameborder="0" height="100%" width="100%">CONSULTA DE ARCHIVO DE SOPORTE SOLICITUD DE ALTA DE CUENTA BANCARIA</iframe>');
                $(this.$refs.modalPDF).draggable();
                $(this.$refs.modalPDF).modal('show');
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.cancelar()
                    }
                });
            },
            cancelar() {
                return this.$store.dispatch('finanzas/solicitud-alta-cuenta-bancaria/cancelar', {
                    id: this.id,
                    params: { include: ['moneda', 'subcontrato','empresa','banco','tipo','plaza','movimientos','movimientos.usuario','movimiento_solicitud'], data:[this.$data.observaciones]}
                }).then(data => {
                    this.$store.commit('finanzas/solicitud-alta-cuenta-bancaria/UPDATE_CUENTA', data)
                    $(this.$refs.modal).modal('hide');
                })
                    .finally( ()=>{
                        this.cargando = false;
                    });
            }
        },
        computed: {
            solicitudAlta() {
                return this.$store.getters['finanzas/solicitud-alta-cuenta-bancaria/currentCuenta'];
            }
        }
    }
</script>

<style scoped>

</style>