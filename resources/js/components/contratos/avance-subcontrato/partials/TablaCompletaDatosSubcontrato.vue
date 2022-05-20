<template>
    <div class="row" v-if="subcontrato">
        <div class="col-md-12">
            <span><i class="fa fa-envelope"></i>Datos de Subcontrato</span>
            <div class="table-responsive">
                <table class="table  table-sm">
                    <tr>
                        <th class="encabezado" >
                            Empresa
                        </th>
                        <th class="encabezado">
                            Contrato Antecedente
                        </th>
                        <th class="encabezado">
                            Referencia
                        </th>
                        <th class="encabezado">
                            Monto
                        </th>
                    </tr>
                    <tr>
                        <td>
                            {{subcontrato.empresa}}
                        </td>
                        <td>
                            {{subcontrato.contrato_proyectado.numero_folio_format}}
                        </td>
                        <td>
                            {{subcontrato.referencia}}
                        </td>
                        <td style="text-align: right">
                            {{subcontrato.monto_format}}
                        </td>
                    </tr>
                    <tr>
                        <th style="border: none"></th>
                    </tr>
                    <tr>
                        <th colspan="4" class="encabezado">
                            Observaciones
                        </th>
                    </tr>
                    <tr>
                        <td colspan="4">
                            {{subcontrato.observaciones}}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "SubcontratoTablaCompletaDatos",
        props: ['subcontrato'],
    }
</script>

<style scoped>
    .encabezado{
        text-align: center; background-color: #f2f4f5
    }
    td, th{
        border: 1px #dee2e6 solid;
    }
</style>
