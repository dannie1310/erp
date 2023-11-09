<template>
    <div class="row" v-if="documentos">
        <div class="col-md-12">
           <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                    <tr>
                        <th class="index_corto encabezado">#</th>
                        <th class="c100 encabezado">Segmento Negocio</th>
                        <th class="c80 encabezado">Tipo Gasto</th>
                        <th class="c80 encabezado">Importe</th>
                        <th class="c100 encabezado">IVA</th>
                        <th class="c100 encabezado">Retenciones</th>
                        <th class="c100 encabezado">Otros Impuestos</th>
                        <th class="c100 encabezado">Total</th>
                        <th class="c30 encabezado">F</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(d, i) in documentos.data">
                        <td style="text-align:center; vertical-align:inherit;">{{i+1}}</td>
                        <td>{{d.centro_costo}}</td>
                        <td>{{ d.tipoGasto.descripcion }}</td>
                        <td style="text-align:right;">{{ d.importe_format }}</td>
                        <td style="text-align:right;">{{ d.iva_format }}</td>
                        <td style="text-align:right;">{{ d.retenciones_format }}</td>
                        <td style="text-align:right;">{{ d.otros_imp_format }}</td>
                        <td style="text-align:right;">{{ d.total_format }}</td>
                        <td>{{d.facturable}}</td>
                   </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "documentos",
    props: ['documentos'],
    methods :{
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
