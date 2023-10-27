<template>
    <div class="row" v-if="relacion">
        <div class="col-md-12">
            <span><i class="fa fa-file-invoice"></i>Datos de la Relación</span>
            <div class="table-responsive">
                <table class="table table-sm">
                    <tr>
                        <th class="encabezado">
                            Empresa
                        </th>
                        <th class="encabezado">
                            Empleado
                        </th>
                        <th class="encabezado">
                            Departamento
                        </th>
                    </tr>
                    <tr>
                        <th>
                            {{relacion.empresa_descripcion}}
                        </th>
                        <th>
                            {{relacion.empleado_descripcion}}
                        </th>
                        <th>
                            {{relacion.departamento}}
                        </th>
                    </tr>
                    <tr>
                        <th class="encabezado  c130">
                           Proyecto
                        </th>
                        <th class="encabezado  c130">
                            Fecha Inicial
                        </th>
                        <th class="encabezado  c130">
                            Fecha Final
                        </th>
                    </tr>
                    <tr>
                        <td>
                            {{relacion.proyecto_descripcion}}
                        </td>
                        <td>
                            {{relacion.fecha_inicio_format}}
                        </td>
                        <td>
                            {{ relacion.fecha_final_format }}
                        </td>
                    </tr>
                    <tr>
                        <th class="encabezado">
                            Motivo
                        </th>
                        <th class="encabezado">
                            Moneda
                        </th>
                        <th class="encabezado">
                            Estado
                        </th>
                    </tr>
                    <tr>
                        <td>
                            {{ relacion.motivo }}
                        </td>
                        <td>
                            {{ relacion.moneda }}
                        </td>
                        <td style="text-align: center">
                            <estado v-bind:value="getEstado(relacion.estado_descripcion, relacion.estado_color)" />
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-md-12">
            <span><i class="fa fa-file-invoice"></i>Documentos asociados</span>
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th class="index_corto encabezado">#</th>
                            <th class="c100 encabezado">Tipo de Documento</th>
                            <th class="c80 encabezado">Fecha</th>
                            <th class="c80 encabezado">Folio</th>
                            <th class="c100 encabezado">Concepto</th>
                            <th class="c100 encabezado">Importe</th>
                            <th class="c100 encabezado">IVA</th>
                            <th class="c100 encabezado">Retenciones</th>
                            <th class="c100 encabezado">Otros Imp.</th>
                            <th class="c100 encabezado">Total</th>
                            <th class="c100 encabezado">No. Personas</th>
                            <th class="c100 encabezado">Observaciones</th>
                            <th class="c100 encabezado">Uuid</th>
                            <th class="c100 encabezado">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(d, i) in relacion.documentos.data">
                            <td style="text-align:center; vertical-align:inherit;">{{i+1}}</td>
                            <td>{{d.tipoDocumento.descripcion}}</td>
                            <td>{{ d.fecha_format }}</td>
                            <td>{{ d.folio }}</td>
                            <td>{{ d.tipoGasto.descripcion }}</td>
                            <td style="text-align:right;">{{ d.importe_format }}</td>
                            <td style="text-align:right;">{{ d.iva_format }}</td>
                            <td style="text-align:right;">{{ d.retenciones_format }}</td>
                            <td style="text-align:right;">{{ d.otros_imp_format }}</td>
                            <td style="text-align:right;">{{ d.total_format }}</td>
                            <td>{{ d.no_personas }}</td>
                            <td>{{ d.observaciones }}</td>
                            <td>{{ d.uuid }}</td>
                            <td> <estado v-bind:value="getEstado(d.estado_descripcion, d.estado_color)" /></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="encabezado"></td>
                            <td class="encabezado"></td>
                            <td class="encabezado"></td>
                            <td class="encabezado"></td>
                            <th class="encabezado">Sumatoria</th>
                            <td class="encabezado" style="text-align:right;">{{ relacion.suma_importe_format }}</td>
                            <td class="encabezado" style="text-align:right;">{{ relacion.suma_iva_format }}</td>
                            <td class="encabezado" style="text-align:right;">{{ relacion.suma_retenciones_format }}</td>
                            <td class="encabezado" style="text-align:right;">{{ relacion.suma_otros_imp_format }}</td>
                            <td class="encabezado" style="text-align:right;">{{ relacion.total_format }}</td>
                            <td class="encabezado"></td>
                            <td class="encabezado"></td>
                            <td class="encabezado"></td>
                            <td class="encabezado"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group error-content">
                <label for="observaciones" class="col-form-label">Observaciones: </label>
                <h5>{{relacion.motivo}}</h5>
            </div>
        </div>
    </div>
</template>

<script>
import estado from './EstatusLabel'
export default {
    name: "RelacionGastosTablaDatos",
    components: { estado },
    props: ['relacion'],
    methods :{
        getEstado(estado, color) {
            return {
                color: color,
                descripcion: estado
            }
        },
    }
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
