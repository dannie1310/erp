<template>
    <span>
        <encabezado-contrato-proyectado v-bind:contrato_proyectado="contrato_proyectado"></encabezado-contrato-proyectado>
        <div class="row" v-if="contrato_proyectado">
            <div class="col-md-12">
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
import ButtonsContratoProyectado from "./ActionButtons";
import EncabezadoContratoProyectado from "../Encabezado";
export default {
    name: "DatosContratoProyectado",
    components: {EncabezadoContratoProyectado, ButtonsContratoProyectado},
    props: ['contrato_proyectado'],
}
</script>

<style scoped>
.encabezado{
    text-align: center; background-color: #f2f4f5
}

</style>
