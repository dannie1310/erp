<template>
    <div class="row" v-if="subcontrato">
        <div class="col-md-12">
            <span><i class="fa fa-file-contract"></i>Datos de Subcontrato</span>
            <table class="table table-bordered table-sm">
                <tr>
                    <th class="encabezado">
                        Contratista
                    </th>
                    <th class="encabezado">
                        Folio de Subcontrato
                    </th>
                    <th class="encabezado">
                        Folio de Contrato
                    </th>
                    <th class="encabezado">
                        Referencia de Subcontrato
                    </th>
                    <th class="encabezado">
                        Fecha de Subcontrato
                    </th>
                    <th class="encabezado">
                        Monto de Subcontrato
                    </th>
                </tr>
                <tr>
                    <td>
                        {{ subcontrato.empresa }}
                    </td>
                    <td style="text-align: center">
                        {{subcontrato.numero_folio_format}}
                    </td>
                    <td style="text-align: center">
                        <router-link :to="{name: 'proyectado-show', params:{id : subcontrato.id_contrato_proyectado }}" target="_blank">
                            <b><span style="color:black; text-decoration: underline">{{subcontrato.contrato_folio_format}}</span></b>
                        </router-link>
                    </td>
                    <td>
                        {{ subcontrato.referencia }}
                    </td>
                    <td style="text-align: center">
                        {{ subcontrato.fecha_format }}
                    </td>
                    <td style="text-align: center">
                        {{ subcontrato.monto_format }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</template>

<script>

export default {
    name: "TablaDatosSubcontrato",
    props: ['subcontrato'],
}
</script>

<style scoped>
.encabezado{
    text-align: center; background-color: #f2f4f5
}

</style>
