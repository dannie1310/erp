<template>
    <div class="row" v-if="documentos">
        <hr />
        <div  class="col-md-12 table-responsive-xl">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                    <tr>
                        <th class="index_corto encabezado">#</th>
                        <th class="c100 encabezado">Tipo de Documento</th>
                        <th class="c80 encabezado">Fecha</th>
                        <th class="c80 encabezado">Folio</th>
                        <th class="c100 encabezado">Concepto</th>
                        <th class="c100 encabezado">Concepto del CFDI</th>
                        <th class="c100 encabezado">Importe</th>
                        <th class="c100 encabezado">IVA</th>
                        <th class="c100 encabezado">2% H</th>
                        <th class="c100 encabezado">Total</th>
                        <th class="c100 encabezado">Observaciones</th>
                        <th class="c100 encabezado">Uuid</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(partida, i) in documentos.data">
                        <td style="text-align:center; vertical-align:inherit;">{{i+1}}</td>
                        <td>
                            {{ partida.tipo_docto_descripcion }}
                        </td>
                        <td>
                            {{ partida.fecha }}
                        </td>
                        <td>
                            {{ partida.folio_docto }}
                        </td>
                        <td>
                            {{ partida.tipo_gasto_descripcion }}
                        </td>
                        <td v-if="partida.uuid != null">
                            {{ partida.concepto_xml }}
                        </td>
                        <td v-else>-</td>
                        <td style="text-align: right">
                            {{ parseFloat(partida.importe).formatMoney(2) }}
                        </td>
                        <td style="text-align: right">
                            $ {{ parseFloat(partida.iva).formatMoney(2)}}
                        </td>
                        <td style="text-align: right">
                            $
                        </td>
                        <td style="text-align: right">
                            $ {{ parseFloat(partida.total).formatMoney(2) }}
                        </td>
                        <td>
                            {{ partida.observaciones }}
                        </td>
                        <td v-if="partida.uuid != null">
                            {{ partida.uuid }}
                        </td>
                        <td v-else>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import datepicker from 'vuejs-datepicker';
import {es} from 'vuejs-datepicker/dist/locale';
export default {
    name: "TablaDatos",
    components: { datepicker, es },
    props: ['documentos'],
    data(){
        return{
            es: es
        }
    },
    methods :{
        formatoFecha(date){
            return moment(date).format('DD/MM/YYYY');
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
