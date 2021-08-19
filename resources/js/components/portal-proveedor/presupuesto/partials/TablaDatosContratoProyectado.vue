<template>
    <span v-if="contrato_proyectado">
        <div class="row">
            <div class="col-md-12">
                <span><i class="fa fa-comment-dots"></i>Datos de Contrato Proyectado</span>
                <table class="table table-bordered table-sm">
                    <tr >
                        <th class="encabezado">
                            Área Subcontratante
                        </th>
                        <th class="encabezado">
                            Folio
                        </th>
                        <th class="encabezado">
                            Referencia
                        </th>
                        <th class="encabezado">
                            Fecha
                        </th>
                    </tr>
                    <tr>
                        <td>
                            {{contrato_proyectado.area_subcontratante}}
                        </td>
                        <td>
                            {{contrato_proyectado.numero_folio_format}}
                        </td>
                        <td>
                            {{contrato_proyectado.referencia}}
                        </td>
                        <td>
                            {{contrato_proyectado.fecha_format}}
                        </td>
                    </tr>
                    <template v-if="contrato_proyectado.observaciones!='...'">
                        <tr>
                            <th colspan="4" class="encabezado">
                                Observaciones
                            </th>
                        </tr>
                        <tr>
                            <td colspan="4">
                                {{contrato_proyectado.observaciones}}
                            </td>
                        </tr>
                    </template>
                </table>
            </div>
        </div>
    </span>

</template>

<script>
export default {
    name: "TablaDatosContratoProyectado",
    props: ['contrato_proyectado'],
}
</script>

<style scoped>
.encabezado{
    text-align: center; background-color: #f2f4f5
}

</style>
