<template>
    <span>
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="card" >
                    <div class="card-header">
                        <h6>
                            <i class="fa fa-file-powerpoint"></i>Solicitud de Pago Anticipado {{solicitud.solicitud_pago_anticipado.numero_folio_format}}
                        </h6>
                    </div>
                    <div class="card-body">


                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label style="font-weight: normal">
                                    Proyecto:
                                </label>
                                <div style="font-weight: bold">{{solicitud.solicitud_pago_anticipado.obra.nombre}}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="form-group">
                                    <label style="font-weight: normal">
                                        Proveedor / Contratista:
                                    </label>
                                    <div style="font-weight: bold">
                                        {{solicitud.solicitud_pago_anticipado.empresa.razon_social}}
                                    </div>
                                </div>

                            </div>
                        </div>


                        <div class="col-md-2">
                            <div class="form-group">
                                <label style="font-weight: normal">
                                    Monto Solicitado:
                                </label>
                                <div style="font-weight: bold; text-align: right">
                                    {{solicitud.solicitud_pago_anticipado.monto_format}}
                                </div>

                            </div>
                        </div>
                    </div>

                        <hr>
                        Datos de Solicitud de Pago Anticipado
                        <hr>

                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label style="font-weight: normal">
                                    Folio:
                                </label>
                                <div style="font-weight: bold; ">
                                    {{solicitud.solicitud_pago_anticipado.numero_folio_format}}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label style="font-weight: normal">
                                    Fecha:
                                </label>
                                <div style="font-weight: bold; ">
                                    {{solicitud.solicitud_pago_anticipado.fecha_format}}
                                </div>

                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label style="font-weight: normal">
                                    Vencimiento:
                                </label>
                                <div style="font-weight: bold;">
                                    {{solicitud.solicitud_pago_anticipado.fecha_vencimiento_format}}
                                </div>

                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label style="font-weight: normal">
                                    Monto:
                                </label>
                                <div style="font-weight: bold; text-align: right">
                                    {{solicitud.solicitud_pago_anticipado.monto_format}}
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group error-content">
                                <label style="font-weight: normal">
                                    Observaciones:
                                </label>
                                <div style="font-weight: bold; ">
                                    {{solicitud.solicitud_pago_anticipado.observaciones}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                            <hr>
                            Datos de Transacción Antecedente
                            <hr>

                        <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label style="font-weight: normal">
                                    Fecha:
                                </label>
                                <div style="font-weight: bold; ">
                                    {{solicitud.solicitud_pago_anticipado.transaccionAntecedenteSinGlobalScope.fecha_format}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label style="font-weight: normal">
                                    Folio:
                                </label>
                                <div style="font-weight: bold; ">
                                    {{solicitud.solicitud_pago_anticipado.transaccion_antecedente_txt}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label style="font-weight: normal">
                                    Monto:
                                </label>
                                <div style="font-weight: bold; ">
                                    {{solicitud.solicitud_pago_anticipado.transaccionAntecedenteSinGlobalScope.monto_format}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group error-content">
                                <label style="font-weight: normal">
                                    Observaciones:
                                </label>
                                <div style="font-weight: bold; ">
                                    {{$solicitud.solicitud_pago_anticipado.transaccion_antecedente_observaciones}}
                                </div>
                            </div>
                        </div>
                    </div>



                    </div>
                    <div class="card-footer">
                        <div class="pull-right">
                            <button type="button" class="btn btn-danger" v-on:click="autorizar"><i class="fa fa-thumbs-o-up"></i>Autorizar</button>
                            <button type="button" class="btn btn-warning"  v-on:click="cancelar"><i class="fa fa-thumbs-o-down"></i>Rechazar</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </span>
</template>

<script>
    export default {
        name: "autorizar-solicitud-pago",
        props: ['id'],
        data() {
            return {
                cargando : true
            }
        },
        methods: {
            find() {
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
                this.$store.commit('finanzas-general/solicitud-pago/SET_SOLICITUD', null);
                return this.$store.dispatch('finanzas-general/solicitud-pago/find', {
                    id: this.id,
                    params: {}
                }).then(data => {
                    this.$store.commit('finanzas-general/solicitud-pago/SET_SOLICITUD', data);
                }).finally(()=>
                {
                    this.cargando = false;
                })
            },
        },
        computed: {
            solicitud() {
                return this.$store.getters['finanzas-general/solicitud-pago/currentSolicitud']
            }
        }
    }
</script>

<style scoped>

</style>
