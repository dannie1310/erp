<template>
    <div class="row" v-if="solicitud">
        <div class="col-md-12">
            <span><i class="fa fa-comment-dollar"></i>Datos de {{solicitud.tipo}}</span>
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <tr>
                        <th class="encabezado" colspan="5">
                            {{solicitud.empresa}}
                        </th>
                    </tr>
                    <tr>
                        <th class="encabezado">
                            Destinatario
                        </th>
                        <th class="encabezado">
                            Concepto
                        </th>
                        <th class="encabezado">
                            Fecha
                        </th>
                        <th class="encabezado">
                            Importe Autorizado
                        </th>
                        <th class="encabezado">
                            Vencimiento
                        </th>
                    </tr>
                    <tr>
                        <td>
                            {{solicitud.empresa}}
                        </td>
                        <td style="text-align: center">
                            {{solicitud.costo}}
                        </td>
                        <td style="text-align: center">
                            {{solicitud.fecha}}
                        </td>
                        <td style="text-align: right">
                            ${{parseFloat(solicitud.remesa.monto_autorizado_remesa).formatMoney(2, '.', ',')}}
                        </td>
                        <td style="text-align: center">
                            {{solicitud.fecha_vencimiento_format}}
                        </td>
                    </tr>
                    <template v-if="solicitud.observaciones!=''">
                        <tr>
                            <th colspan="5" class="encabezado">
                                Observaciones
                            </th>
                        </tr>
                        <tr>
                            <td colspan="5">
                                {{solicitud.observaciones}}
                            </td>
                        </tr>
                    </template>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "DatosDocumento",
        props: ['solicitud'],
    }
</script>

<style scoped>
    .encabezado{
        text-align: center; background-color: #f2f4f5
    }
</style>
