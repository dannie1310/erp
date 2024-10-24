<template>
    <div class="row" v-if="reembolso">
        <div class="col-md-12" style="text-align: right;">
            <h5><b>Con Segmentos Cargados (TOTALES)</b></h5>
        </div>
        <div class="col-md-12">
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
                            Caja Chica
                        </th>
                    </tr>
                    <tr>
                        <th>
                            {{reembolso.empresa_descripcion}}
                        </th>
                        <th>
                            {{reembolso.relacion.data[0].empleado_descripcion}}
                        </th>
                        <th colspan="3">
                            {{ reembolso.empleado_descripcion }} ({{ reembolso.alias_dep }})
                        </th>
                    </tr>
                    <tr>
                        <th class="encabezado" colspan="3">
                            Concepto
                        </th>
                    </tr>
                    <tr>
                        <th colspan="3">
                            {{reembolso.concepto}}
                        </th>
                    </tr>
                    <tr>
                        <th class="encabezado  c130">
                            Fecha
                        </th>
                        <th class="encabezado  c130">
                            Fecha Limite de Pago
                        </th>
                        <th class="encabezado">
                            Estado
                        </th>
                    </tr>
                    <tr>
                        <td>
                            {{reembolso.fecha_inicio_format}}
                        </td>
                        <td>
                            {{reembolso.fecha_final_format}}
                        </td>
                        <td style="text-align: center">
                            <estado v-bind:value="getEstado(reembolso.relacion.data[0].estado_descripcion, reembolso.relacion.data[0].estado_color)" />
                        </td>
                    </tr>
                    <tr>
                        <th class="encabezado">Moneda</th>
                        <th class="encabezado">Importe</th>
                        <th class="encabezado">IVA</th>
                    </tr>
                    <tr>
                        <td><b>{{ reembolso.moneda }}</b></td>
                        <td style="text-align: right; font-size: 15px"><b>{{ reembolso.suma_importe_format }}</b></td>
                        <td style="text-align: right; font-size: 15px"><b>{{ reembolso.suma_iva_format }}</b></td>
                    </tr>
                    <tr>
                        <th class="encabezado">Retenciones</th>
                        <th class="encabezado">Otros Impuestos</th>
                        <th class="encabezado">Total</th>
                    </tr>
                    <tr>
                        <td style="text-align: right; font-size: 15px"><b>{{ reembolso.suma_retenciones_format}}</b></td>
                        <td style="text-align: right; font-size: 15px"><b>{{ reembolso.suma_otros_imp_format }}</b></td>
                        <td style="text-align: right; font-size: 15px"><b>{{ reembolso.total_format }}</b></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import datepicker from 'vuejs-datepicker';
import estado from './EstatusLabel';
import {es} from 'vuejs-datepicker/dist/locale';
export default {
    name: "TablaReembolso",
    components: { datepicker, es, estado },
    props: ['reembolso'],
    data(){
        return{
            es: es,
            proyectos : []
        }
    },
    mounted() {
        this.getProyectos();
        this.getCajaChica();
    },
    methods :{
        formatoFecha(date){
            return moment(date).format('DD/MM/YYYY');
        },
        getProyectos() {
            return this.$store.dispatch('controlRecursos/proveedor/index', {
                params: { scope:'paraReembolso' }
            }).then(data => {
                this.proyectos = data.data;
            })
        },
        getCajaChica() {
            return this.$store.dispatch('controlRecursos/caja-chica/index', {
                params: { scope: 'cajaChica' }
            }).then(data => {
                this.cajas = data.data;
            })
        },
        getEstado(estado, color) {
            return {
                es: es,
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
