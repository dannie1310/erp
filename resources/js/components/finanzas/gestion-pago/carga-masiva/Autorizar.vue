<template>
    <div class="row">
        <div class="col-12">
            <div class="invoice p-3 mb-3">
                <div class="row">
                    <div class="col-12">
                        <h4> <i class="fa fa-list-alt"></i> AUTORIZACIÓN DE LAYOUTS REGISTRADOS</h4>
                    </div>
                </div>
                <!--                <div class="modal-body">-->
                <div class="row">
                    <div class="table-responsive col-12">
                        <table class="table table-striped">
                            <tbody>
                            <tr>
                                <td class="bg-gray-light">
                                    <b>Folio Dispersión:</b>
                                </td>
                                <td class="bg-gray-light">
                                    9
                                </td>

                                <td class="bg-gray-light">
                                    <b>Monto Total de Remesa:</b>
                                </td>
                                <td class="bg-gray-light text-right">
                                    $&nbsp; 4000
                                </td>
                                <td class="bg-gray-light">
                                    <b>Monto de Está Dispersion:</b>
                                </td>
                                <td class="bg-gray-light text-right">
                                    $ 4000554
                                </td>
                                <td class="bg-gray-light"><b>Estado:</b><br> </td>
<!--                                <td class="bg-gray-light"><estatus-label></estatus-label></td>-->
                            </tr>
                            <tr>
                                <td colspan="2" class="bg-gray-light">
                                    <b>Registró:</b>
                                </td>
                                <td colspan="2" class="bg-gray-light">
                                  Jaun
                                </td>
                                <td colspan="2" class="bg-gray-light">
                                    <b>Fecha de Registro:</b>
                                </td>
                                <td colspan="2" class="bg-gray-light">
                                    2019-09-03 22:24:00
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" class="bg-gray-light">
                                    <b>Canceló</b>
                                </td>
                                <td colspan="4" class="bg-gray-light">
                                    {{distribucion.usuario_cancelo.nombre
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-responsive col-12">
                        <h5><i class="fa fa-info-circle" style="padding-right: 3px"></i>Información de la Remesa</h5>
                        <table class="table table-striped">
                            <tbody>
                            <tr>
                                <td colspan="2" class="bg-gray-light">
                                    <b>Año:</b><br>
                                 2019
                                </td>
                                <td colspan="2" class="bg-gray-light">
                                    <b>Semana:</b><br>
                               30
                                </td>
                                <td colspan="2" class="bg-gray-light">
                                    <b>Tipo de Remesa: </b>
                                    <br>Extraordinaria
                                </td>
                                <td colspan="2" class="bg-gray-light">
                                    <b>Folio: <br>(52)</b>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <h5><i class="fa fa-list" style="padding-right: 3px"></i>Partidas del Layout</h5>
<!--                <div v-if="" class="row">-->
<!--                    <div  class="col-12 table-responsive">-->
<!--                        <table class="table table-striped">-->
<!--                            <thead>-->
<!--                            <tr>-->
<!--                                <th>#</th>-->
<!--                                <th>Concepto</th>-->
<!--                                <th>Beneficiario</th>-->
<!--                                &lt;!&ndash; <th>Importe</th>-->
<!--                                   <th>Tipo Cambio</th>-->
<!--                                   <th>Importe con TC</th>-->
<!--                                   <th>Tipo Cambio Actual</th>&ndash;&gt;-->
<!--                                <th>Importe Pesos</th>-->
<!--                                <th>Cuenta Cargo</th>-->
<!--                                <th>Cuenta Abono</th>-->
<!--                                <th>Estado</th>-->
<!--                            </tr>-->
<!--                            </thead>-->
<!--                            <tbody>-->
<!--                            <tr v-for="(doc, i) in distribucion.partidas.data">-->
<!--                                <td>{{i+1}}</td>-->
<!--                                <td>{{doc.documento.concepto}}</td>-->
<!--                                <td>{{doc.documento.beneficiario}}</td>-->
<!--                                <td class="text-right">${{doc.moneda && doc.moneda.tipo != 1? parseFloat((doc.documento.monto_total * doc.moneda.tipo_cambio)).formatMoney(2, '.', ',') : parseFloat((doc.documento.monto_total)).formatMoney(2, '.', ',')}}</td>-->
<!--                                <td>{{ doc.cuentaCargo.abreviatura }} ({{doc.cuentaCargo.numero}})</td>-->
<!--                                <td>{{getCuentaAbono(doc.cuentaAbono)}}</td>-->
<!--                                <td><partida-estatus :value="doc.estado"></partida-estatus></td>-->
<!--                            </tr>-->
<!--                            </tbody>-->
<!--                        </table>-->
<!--                    </div>-->
<!--                </div>-->
                <!--                </div>-->

                <div class="modal-footer">
                    <button  type="button" class="btn btn-secondary pull-right" v-on:click="salir">Cerrar</button>
                    <div>
                        <button  @click="autorizar" title="Autorizar" class="btn btn-primary pull-right">Autorizar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Autorizar",
        props: ['value'],
        data() {
            return {
                id:'',
            }
        },
        mounted() {
            this.id = this.$route.params.id;

        },
        methods: {
            autorizar (){

            },
            salir(){

            }

        },
        computed: {

        }
    }
</script>

<style scoped>

</style>
|
