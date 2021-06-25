<template>
    <div class="row" v-if="presupuesto">
        <div class="col-md-12">
            <span><i class="fa fa-comments-dollar"></i>Datos de Presupuesto</span>
            <table class="table table-bordered table-sm">
                <tr >
                    <th class="encabezado">
                        Contratista
                    </th>
                    <th class="encabezado">
                        Folio de Presupuesto
                    </th>
                    <th class="encabezado">
                        Folio de Contrato
                    </th>
                    <th class="encabezado">
                        Referencia de Contrato
                    </th>
                    <th class="encabezado">
                        Fecha de Presupuesto
                    </th>
                    <th class="encabezado">
                        Monto de Presupuesto
                    </th>
                    <th class="encabezado">
                        Moneda
                    </th>
                </tr>
                <tr>
                    <td>
                        {{presupuesto.empresa.razon_social}}
                    </td>
                    <td style="text-align: center">
                        {{presupuesto.numero_folio_format}}
                    </td>
                    <td style="text-align: center">
                        <router-link :to="{name: 'proyectado-show', params:{id : presupuesto.contrato_proyectado.id }}" target="_blank">
                            <b><span style="color:black; text-decoration: underline">{{presupuesto.contrato_proyectado.numero_folio_format}}</span></b>
                        </router-link>
                    </td>
                    <td>
                        {{ presupuesto.contrato_proyectado.referencia }}
                    </td>
                    <td style="text-align: center">
                        {{ presupuesto.fecha_format }}
                    </td>
                    <td style="text-align: center">
                        {{presupuesto.monto_format}}
                    </td>
                    <td style="text-align: center">
                        {{presupuesto.moneda_conversion}}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</template>

<script>

export default {
    name: "TablaDatosPresupuesto",
    props: ['presupuesto'],
}
</script>

<style scoped>
.encabezado{
    text-align: center; background-color: #f2f4f5
}

</style>
