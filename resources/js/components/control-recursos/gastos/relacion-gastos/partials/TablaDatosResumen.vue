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
                        <th class="encabezado  c130" colspan="2">
                           Proveedor
                        </th>
                        <th class="encabezado">
                            Estado
                        </th>
                    </tr>
                    <tr>
                        <td colspan="2">
                            {{relacion.proyecto_descripcion}}
                        </td>
                        <td style="text-align: center">
                            <estado v-bind:value="getEstado(relacion.estado_descripcion, relacion.estado_color)" />
                        </td>
                    </tr>
                    <tr>
                        <th class="encabezado">
                            Concepto
                        </th>
                        <th class="encabezado  c130">
                            Fecha
                        </th>
                        <th class="encabezado  c130">
                            Fecha Limite de Pago
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <input name="motivo"
                                id="motivo"
                                class="form-control"
                                v-model="relacion.motivo"
                                v-validate="{required: true}"
                                data-vv-as="Motivo"
                                :class="{'is-invalid': errors.has('motivo')}" />
                            <div class="invalid-feedback" v-show="errors.has('motivo')">{{ errors.first('motivo') }}</div>
                        </td>
                        <td>
                            <datepicker v-model = "relacion.fecha_inicio_editar"
                                        name = "fecha_inicial"
                                        :format = "formatoFecha"
                                        data-vv-as="Fecha Inicial"
                                        :language = "es"
                                        :bootstrap-styling = "true"
                                        class = "form-control"
                                        v-validate="{required: true}"
                                        :class="{'is-invalid': errors.has('fecha_inicial')}"/>
                            <div class="invalid-feedback" v-show="errors.has('fecha_inicial')">{{ errors.first('fecha_inicial') }}</div>
                        </td>
                        <td>
                            <datepicker v-model = "relacion.fecha_final_editar"
                                        name = "fecha_final"
                                        data-vv-as="Fecha Final"
                                        :format = "formatoFecha"
                                        :language = "es"
                                        :bootstrap-styling = "true"
                                        class = "form-control"
                                        v-validate="{required: true}"
                                        :class="{'is-invalid': errors.has('fecha_final')}"/>
                            <div class="invalid-feedback" v-show="errors.has('fecha_final')">{{ errors.first('fecha_final') }}</div>
                        </td>
                    </tr>
                    <tr>
                        <th class="encabezado">Moneda</th>
                        <th class="encabezado">Importe</th>
                        <th class="encabezado">IVA</th>
                    </tr>
                    <tr>
                        <td>{{ relacion.moneda }}</td>
                        <td style="text-align: right; font-size: 15px"><b>{{ relacion.suma_importe_format }}</b></td>
                        <td style="text-align: right; font-size: 15px"><b>{{ relacion.suma_iva_format }}</b></td>
                    </tr>
                    <tr>
                        <th class="encabezado">Retenciones</th>
                        <th class="encabezado">Otros Impuestos</th>
                        <th class="encabezado">Total</th>
                    </tr>
                    <tr>
                        <td style="text-align: right; font-size: 15px"><b>{{ relacion.suma_retenciones_format}}</b></td>
                        <td style="text-align: right; font-size: 15px"><b>{{ relacion.suma_otros_imp_format }}</b></td>
                        <td style="text-align: right; font-size: 15px"><b>{{ relacion.total_format }}</b></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import estado from './EstatusLabel';
import datepicker from 'vuejs-datepicker';
import {es} from 'vuejs-datepicker/dist/locale';
export default {
    name: "RelacionGastosTablaDatos",
    components: { estado, datepicker, es },
    props: ['relacion'],
    data(){
        return{
            es: es
        }
    },
    methods :{
        formatoFecha(date){
            return moment(date).format('DD/MM/YYYY');
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
