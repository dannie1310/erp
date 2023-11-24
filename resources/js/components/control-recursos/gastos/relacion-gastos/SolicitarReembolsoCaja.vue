<template>
    <span>
        <div class="card" v-if="relacion == null">
            <div class="card-body">
                <div class="row" >
                    <div class="col-md-12">
                        <div class="spinner-border text-success" role="status">
                           <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card" v-else>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <h4>Documento para Relación</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                         <encabezado v-bind:relacion="relacion" />
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
                                               Proyecto
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
                                        <tr>
                                            <th class="encabezado" colspan="3">Caja Chica</th>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <div class="form-group error-content">
                                                    <select class="form-control"
                                                            data-vv-as="Caja Chica"
                                                            id="id_caja"
                                                            name="id_caja"
                                                            :class="{'is-invalid': errors.has('id_caja')}"
                                                            v-validate="{required: true}"
                                                            v-model="id_caja">
                                                        <option value>-- Selecionar --</option>
                                                        <option v-for="(c) in cajas" :value="c.id">{{ c.descripcion }}</option>
                                                    </select>
                                                    <div style="display:block" class="invalid-feedback" v-show="errors.has('id_caja')">{{ errors.first('id_caja') }}</div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0" @click="validate"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-secondary" v-on:click="salir"><i class="fa fa-angle-left"></i>Regresar</button>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import Encabezado from './partials/Encabezado';
import Estado from './partials/EstatusLabel';
import {es} from "vuejs-datepicker/dist/locale";
import datepicker from 'vuejs-datepicker';
export default {
    name: "SolicitaReembolsoXCaja",
    components: { Encabezado, Estado, datepicker},
    props: ['id'],
    data(){
        return{
            es : es,
            cargando: false,
            relacion : null,
            cajas: [],
            id_caja: ''
        }
    },
    mounted() {
        this.find();
        this.getCajaChica();
    },
    methods: {
        formatoFecha(date){
            return moment(date).format('DD/MM/YYYY');
        },
        find() {
            this.cargando = true;
            return this.$store.dispatch('controlRecursos/relacion-gasto/find', {
                id: this.id,
                params:{include: []}
            }).then(data => {
                this.relacion = data
            })
                .finally(()=> {
                    this.cargando = false;
                })
        },
        salir() {
            this.$router.push({name: 'relacion-gasto'});
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result)
                {
                    if(moment(this.relacion.fecha_final_editar).format('DD/MM/YYYY') < moment(this.relacion.fecha_inicio_editar).format('DD/MM/YYYY'))
                    {
                        swal('¡Error!', 'La fecha de final no puede ser posterior a la fecha de inicial.', 'error')
                    }
                    else {
                        this.store();
                    }
                }
            });
        },
        store() {
            this.relacion.id_caja = this.id_caja;
            return this.$store.dispatch('controlRecursos/relacion-gasto/', this.relacion)
                .then((data) => {
                    this.relacion = data;
                    this.reembolso()
                });
        },
        reembolso()
        {
            this.$router.push({name:'reembolso-x-caja', params: { id: this.relacion.id_documento }});
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
    },
}
</script>

<style scoped>

</style>
