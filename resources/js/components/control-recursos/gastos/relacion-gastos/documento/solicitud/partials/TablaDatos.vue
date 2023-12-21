<template>
    <div class="row" v-if="reembolso && solicitud">
        <div class="col-md-12" style="text-align: right;">
            <h5><b>Con Segmentos Cargados (TOTALES)</b></h5>
        </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-sm">
                    <tr>
                        <th class="encabezado">
                            Folio de Documentos Asociados
                        </th>
                        <th class="encabezado">
                            Empresa
                        </th>
                        <th class="encabezado">
                            Empleado
                        </th>
                    </tr>
                    <tr>
                        <th>
                            {{reembolso.folio}}
                        </th>
                        <th>
                            {{solicitud.empresa.razon_social}}
                        </th>
                        <th>
                            {{reembolso.empleado_descripcion}}
                        </th>
                    </tr>
                    <tr>
                        <th class="encabezado">
                            Proveedor
                        </th>
                        <th class="encabezado">
                            Concepto
                        </th>
                        <th class="encabezado  c130">
                            Fecha
                        </th>
                    </tr>
                    <tr>
                        <td>
                            {{ solicitud.proveedor.razon_social }}
                        </td>
                        <td>
                            <input name="concepto"
                                   id="concepto"
                                   class="form-control"
                                   v-model="solicitud.concepto"
                                   v-validate="{required: true}"
                                   data-vv-as="Concepto"
                                   :class="{'is-invalid': errors.has('concepto')}" />
                            <div class="invalid-feedback" v-show="errors.has('concepto')">{{ errors.first('concepto') }}</div>
                        </td>
                   
                        <td>
                           {{ solicitud.fecha_format }}
                        </td>
                    </tr>
                    <tr>
                        <th class="encabezado  c130">
                            Fecha Limite de Pago
                        </th>
                        <th class="encabezado">Moneda</th>
                        <th class="encabezado">Importe</th>
                    </tr>
                    <tr>
                        <td>{{ solicitud.fecha_vencimiento }}</td>
                        <td><b>{{ solicitud.moneda }}</b></td>
                        <td style="text-align: right; font-size: 15px"><b>{{ solicitud.importe_format }}</b></td>
                    </tr>
                    <tr>
                        <th class="encabezado">IVA</th>
                        <th class="encabezado">Retenciones</th>
                        <th class="encabezado">Otros Impuestos</th>
                    </tr>
                    <tr>
                        <td style="text-align: right; font-size: 15px"><b>{{ solicitud.iva_format }}</b></td>
                        <td style="text-align: right; font-size: 15px"><b>{{ solicitud.retenciones_format}}</b></td>
                        <td style="text-align: right; font-size: 15px"><b>{{ solicitud.otros_format }}</b></td>
                    </tr>
                    <tr>
                        <th class="encabezado">Total</th>
                        <th class="encabezado">Forma de Pago</th>
                        <th class="encabezado">Tipo de Pago</th>
                    </tr>
                    <tr>
                        <td style="text-align: right; font-size: 15px"><b>{{ solicitud.total_format }}</b></td>
                        <td>
                            <select class="form-control"
                                    data-vv-as="Forma de Pago"
                                    id="forma_pago"
                                    name="forma_pago"
                                    :class="{'is-invalid': errors.has('forma_pago')}"
                                    v-validate="{required: true}"
                                    v-model="solicitud.id_forma_pago">
                                <option value>-- Selecionar --</option>
                                <option v-for="(m) in formas_pago" :value="m.id">{{m.nombre}}</option>
                            </select>
                            <div style="display:block" class="invalid-feedback" v-show="errors.has('forma_pago')">{{ errors.first('forma_pago') }}</div>
                        </td>
                        <td>
                            <label class="form-control">Pago a Proveedor</label>
                        </td>
                    </tr>
                    <tr>
                        <th class="encabezado">Cuenta Bancaria</th>
                        <th class="encabezado">Instrucciones de Entrega</th>
                        <th class="encabezado">Solicitante</th>
                    </tr>
                    <tr>
                        <td v-if="solicitud.id_forma_pago != '' && solicitud.id_forma_pago != 1">
                            <select class="form-control"
                                    data-vv-as="Cuenta Bancaria"
                                    id="cuenta"
                                    name="cuenta"
                                    :class="{'is-invalid': errors.has('cuenta')}"
                                    v-validate="{required: true}"
                                    v-model="solicitud.cuenta">
                                <option value>-- Selecionar --</option>
                                <option v-for="(m) in cuentas" :value="m.id">{{m.banco_descripcion}} {{m.numero_cuenta}} -</option>
                            </select>
                            <div style="display:block" class="invalid-feedback" v-show="errors.has('cuenta')">{{ errors.first('cuenta') }}</div>
                        </td>
                        <td v-else>
                            <label class="form-control"> NO APLICA </label>
                        </td>
                        <td v-if="solicitud.id_forma_pago != ''">
                            <select  class="form-control"
                                     data-vv-as="Forma de Pago"
                                     id="instruccion"
                                     name="instruccion"
                                     :class="{'is-invalid': errors.has('instruccion')}"
                                     v-validate="{required: true}"
                                     v-model="solicitud.id_entrega">
                                <option value>-- Selecionar --</option>
                                <option v-for="(m) in instrucciones" :value="m.id">{{m.descripcion}}</option>
                            </select><div style="display:block" class="invalid-feedback" v-show="errors.has('instruccion')">{{ errors.first('instruccion') }}</div>
                        </td>
                        <td v-else>

                        </td>
                        <td>
                            <select  class="form-control"
                                     data-vv-as="Solicitante"
                                     id="solicitante"
                                     name="solicitante"
                                     :class="{'is-invalid': errors.has('solicitante')}"
                                     v-validate="{required: true}"
                                     v-model="reembolso.id_solicitud">
                                <option value>-- Selecionar --</option>
                                <option v-for="(m) in solicitantes" :value="m.id">{{m.descripcion_st}}</option>
                            </select><div style="display:block" class="invalid-feedback" v-show="errors.has('solicitante')">{{ errors.first('solicitante') }}</div>
                        </td>
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
    props: ['solicitud','reembolso'],
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
