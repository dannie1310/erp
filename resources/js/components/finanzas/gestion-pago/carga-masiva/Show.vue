<template>
    <div class="row">
        <div class="col-12">
            <div class="invoice p-3 mb-3">
                <div class="row">
                    <div class="col-12">
                        <h4> <i class="fa fa-list-alt"></i> DISPERSIÓN DE RECURSOS AUTORIZADOS DE LA REMESA </h4>
                    </div>
                </div>
<!--                <div v-if="true" class="row">-->
                <div class="row">
                    <div class="table-responsive col-12">
                        <table class="table table-striped">
                            <tbody>
                            <tr>
                                <td colspan="2" class="bg-gray-light">
                                    <b>Folio Dispersión:</b>
                                </td>
                                <td colspan="2" class="bg-gray-light">
                                    folio
                                </td>

                                <td class="bg-gray-light">
                                    <b>Monto Total de Remesa:</b>
                                </td>
                                <td class="bg-gray-light text-right">
                                    monto
                                </td>
                                <td class="bg-gray-light"><b>Estado:</b><br> </td>
                                <td class="bg-gray-light">estado</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="bg-gray-light">
                                    <b>Registró:</b>
                                </td>
                                <td colspan="2" class="bg-gray-light">
                                    usuario
                                </td>
                                <td colspan="2" class="bg-gray-light">
                                    <b>Fecha de Registro:</b>
                                </td>
                                <td colspan="2" class="bg-gray-light">
                                    fecha
                                </td>
                            </tr>
<!--                            <tr v-if="true">-->
                            <tr>
                                <td colspan="2" class="bg-gray-light">
                                    <b>Autorizó:</b>
                                </td>
                                <td colspan="2" class="bg-gray-light">
                                    usuario
                                </td>
                                <td colspan="2" class="bg-gray-light">
                                    <b>Fecha de Autorización:</b>
                                </td>
                                <td colspan="2" class="bg-gray-light">
                                    fecha
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
                <h5><i class="fa fa-list" style="padding-right: 3px"></i>Partidas de la Dispersión</h5>
<!--                <div v-if="true" class="row">-->
                <div class="row">
                    <div  class="col-12 table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Concepto</th>
                                <th>Beneficiario</th>
                                <th>Importe Pesos</th>
                                <th>Cuenta Cargo</th>
                                <th>Fecha Pago</th>
                                <th>Tipo Cambio</th>
                                <th>Monto Pago</th>
                                <th>Referencia Pago</th>
                                <th>Estado</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>index</td>
                                <td>concepto</td>
                                <td>empresa</td>
                                <td>importe monto_trance</td>
                                <td>cuenta cargo</td>
                                <td>fecha pago</td>
                                <td>tipo cambio</td>
                                <td>monto pago</td>
                                <td>referencia pago</td>
                                <td>estado</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "pago-masivo-show"
    }
</script>

<style scoped>

</style>