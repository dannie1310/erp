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
                            Proveedor
                        </th>
                    </tr>
                    <tr>
                        <th>
                            {{reembolso.empresa_descripcion}}
                        </th>
                        <th>
                            {{reembolso.empleado_descripcion}}
                        </th>
                        <th>
                            <div class="form-group error-content">
                                <select class="form-control"
                                        data-vv-as="Proyecto"
                                        id="id_proyecto"
                                        name="id_proyecto"
                                        :class="{'is-invalid': errors.has('id_proyecto')}"
                                        v-validate="{required: true}"
                                        v-model="reembolso.id_proveedor">
                                    <option value>-- Selecionar --</option>
                                    <option v-for="(p) in proyectos" :value="p.id">{{ p.razon_social }} ( {{ p.rfc }} )</option>
                                </select>
                                <div style="display:block" class="invalid-feedback" v-show="errors.has('id_proyecto')">{{ errors.first('id_proyecto') }}</div>
                            </div>
                        </th>
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
                                   v-model="reembolso.motivo"
                                   v-validate="{required: true}"
                                   data-vv-as="Motivo"
                                   :class="{'is-invalid': errors.has('motivo')}" />
                            <div class="invalid-feedback" v-show="errors.has('motivo')">{{ errors.first('motivo') }}</div>
                        </td>
                        <td>
                            {{reembolso.fecha_inicio_editar}}
                        </td>
                        <td>
                            <datepicker v-model = "reembolso.fecha_final_editar"
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
import {es} from 'vuejs-datepicker/dist/locale';
export default {
    name: "TablaReembolso",
    components: { datepicker, es },
    props: ['reembolso'],
    data(){
        return{
            es: es,
            proyectos : []
        }
    },
    mounted() {
        this.getProyectos();
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
