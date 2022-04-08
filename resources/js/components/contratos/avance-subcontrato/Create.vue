<template>
    <span>
        <div class="card" v-if="cargando">
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
                <datos v-bind:subcontrato="this.subcontrato" />
                <div class="row">
                    <div class="col-md-8"></div>
                    <div class="col-md-1"><b>Fecha del avance:</b></div>
                    <div class="col-md-3">
                        <div class="form-group error-content">
                            <datepicker v-model = "fecha"
                                        id="fecha"
                                        name = "fecha"
                                        :format = "formatoFecha"
                                        :language = "es"
                                        :bootstrap-styling = "true"
                                        class = "form-control"
                                        v-validate="{required: true}"
                                        :disabled-dates="fechasDeshabilitadas"
                                        :class="{'is-invalid': errors.has('fecha')}"
                            ></datepicker>
                            <div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha') }}</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <tr>
                                    <th class="encabezado" colspan="4">
                                        Periodo de avance
                                    </th>
                                </tr>
                                <tr>
                                    <td>
                                        Inicio:
                                    </td>
                                    <td>
                                        <datepicker v-model = "fecha_inicial"
                                                    name = "fecha_inicial"
                                                    :format = "formatoFecha"
                                                    :language = "es"
                                                    :bootstrap-styling = "true"
                                                    class = "form-control"
                                                    v-validate="{required: true}"
                                                    :disabled-dates="fechasDeshabilitadas"
                                                    :class="{'is-invalid': errors.has('fecha_inicial')}"
                                        ></datepicker>
                                        <div class="invalid-feedback" v-show="errors.has('fecha_inicial')">{{ errors.first('fecha_inicial') }}</div>
                                    </td>
                                    <td>
                                        Término:
                                    </td>
                                    <td>
                                        <datepicker v-model = "fecha_final"
                                                    name = "fecha_final"
                                                    :format = "formatoFecha"
                                                    :language = "es"
                                                    :bootstrap-styling = "true"
                                                    class = "form-control"
                                                    v-validate="{required: true}"
                                                    :disabled-dates="fechasDeshabilitadas"
                                                    :class="{'is-invalid': errors.has('fecha_final')}"
                                        ></datepicker>
                                        <div class="invalid-feedback" v-show="errors.has('fecha_final')">{{ errors.first('fecha_final') }}</div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <tr>
                                    <th class="encabezado" colspan="4">
                                        Fechas de Reconocimiento de Avance
                                    </th>
                                </tr>
                                <tr>
                                    <td>
                                        Ejecución:
                                    </td>
                                    <td>
                                        <datepicker v-model = "fecha_ejecucion"
                                                    name = "fecha_ejecucion"
                                                    :format = "formatoFecha"
                                                    :language = "es"
                                                    :bootstrap-styling = "true"
                                                    class = "form-control"
                                                    v-validate="{required: true}"
                                                    :disabled-dates="fechasDeshabilitadas"
                                                    :class="{'is-invalid': errors.has('fecha_ejecucion')}"
                                        ></datepicker>
                                        <div class="invalid-feedback" v-show="errors.has('fecha_ejecucion')">{{ errors.first('fecha_ejecucion') }}</div>
                                    </td>
                                    <td>
                                        Contable:
                                    </td>
                                    <td>
                                        <datepicker v-model = "fecha_contable"
                                                    name = "fecha_contable"
                                                    :format = "formatoFecha"
                                                    :language = "es"
                                                    :bootstrap-styling = "true"
                                                    class = "form-control"
                                                    v-validate="{required: true}"
                                                    :disabled-dates="fechasDeshabilitadas"
                                                    :class="{'is-invalid': errors.has('fecha_contable')}"
                                        ></datepicker>
                                        <div class="invalid-feedback" v-show="errors.has('fecha_contable')">{{ errors.first('fecha_contable') }}</div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table class="table-responsive table-bordered">
                            <thead>
                                <tr>
                                    <th  class="encabezado c120">Clave</th>
                                    <th class="encabezado">Concepto</th>
                                    <th class="encabezado c100">Unidad</th>
                                    <th class="encabezado">Cantidad Contratada</th>
                                    <th class="encabezado">Precio Unitario</th>
                                    <th class="encabezado">Cantidad</th>
                                </tr>
                            </thead>
                            <tbody v-for="(concepto, i) in conceptos">
                                <tr v-if="concepto.para_estimar == 0">
                                    <td :title="concepto.clave"><b>{{concepto.clave}}</b></td>
                                    <td :title="concepto.descripcion">
                                        <b>{{concepto.descripcion}}</b></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr v-else>
                                    <td :title="concepto.clave">{{ concepto.clave }}</td>
                                    <td :title="concepto.descripcion_concepto">
                                        <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                        {{concepto.descripcion_concepto}}
                                    </td>
                                    <td>{{concepto.unidad}}</td>
                                    <td style="text-align: right">{{concepto.cantidad_subcontrato}}</td>
                                    <td style="text-align: right">{{concepto.precio_unitario_subcontrato_format}}</td>
                                    <td  style="text-align:right">
                                        <input class="text"
                                               v-on:keyup="changeCantidad(concepto)"
                                               v-model="concepto.cantidad_avance"
                                               :name="`cantidadAvance[${i}]`"
                                               data-vv-as="'Cantidad'"
                                               :class="{'is-invalid': errors.has(`cantidadAvance[${i}]`)}" />
                                         <div class="invalid-feedback" v-show="errors.has(`cantidadAvance[${i}]`)">{{ errors.first(`cantidadAvance[${i}]`) }}</div>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td style="border:none" colspan="4"></td>
                                    <td><b>Subtotal</b></td>
                                    <td style="text-align: right"><b>{{parseFloat(subtotal).formatMoney(2)}}</b></td>
                                </tr>
                                <tr>
                                    <td style="border: none" colspan="4"></td>
                                    <td><b>IVA</b></td>
                                    <td style="text-align: right"><b>{{parseFloat(iva).formatMoney(2)}}</b></td>
                                </tr>
                                <tr>
                                    <td style="border: none" colspan="4"></td>
                                    <td><b>Total</b></td>
                                    <td style="text-align: right"><b>{{parseFloat(total).formatMoney(2)}}</b></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <label for="observaciones" class="col-form-label">Observaciones: </label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group row error-content">
                            <textarea
                                name="observaciones"
                                id="observaciones"
                                class="form-control"
                                v-model="observaciones"
                                data-vv-as="Observaciones"
                                :class="{'is-invalid': errors.has('observaciones')}"
                            ></textarea>
                            <div class="invalid-feedback" v-show="errors.has('observaciones')">{{ errors.first('observaciones') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-secondary" v-on:click="salir">
                        <i class="fa fa-angle-left"></i>
                        Regresar</button>
                    <button type="button" @click="validate" :disabled="subcontrato == ''" class="btn btn-primary">
                        <i class="fa fa-save"></i>
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import Datos from './partials/DatosSubcontrato';
    import datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    export default {
        name: "Create",
        props: ['id'],
        components: {Datos, datepicker, es},
        data() {
            return {
                cargando: false,
                es: es,
                subcontrato: null,
                fecha: '',
                fecha_inicial: '',
                fecha_final: '',
                fechasDeshabilitadas:{},
                fecha_ejecucion: '',
                fecha_contable: '',
                conceptos: null,
                subtotal: 0,
                iva: 0,
                total: 0,
                observaciones: null
            }
        },
        mounted() {
            this.fecha = new Date();
            this.fecha_inicial = new Date();
            this.fecha_final = new Date();
            this.fecha_ejecucion =  new Date();
            this.fecha_contable = new Date();
            this.fechasDeshabilitadas.from= new Date();
            this.$validator.reset();
            this.getSubcontrato();
        },
        methods : {
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
            salir() {
                this.$router.push({name: 'avance-subcontrato'});
            },
            getSubcontrato() {
                this.cargando = true;
                return this.$store.dispatch('contratos/subcontrato/find', {
                    id: this.id,
                    params: {include: ['contrato_proyectado']},
                }).then(data => {
                    this.subcontrato = data;
                    this.getConceptos();
                    this.cargando = false;
                })
            },
            getConceptos() {
                this.conceptos = null;
                this.$store.dispatch('contratos/subcontrato/ordenarConceptos', {
                    id: this.id
                }).then(data => {
                    this.conceptos = data.partidas;
                }).finally(() => {
                    this.cargando = false;
                })
            },
            changeCantidad(concepto) {
                if(concepto.cantidad_avance) {
                    this.subtotal += concepto.cantidad_avance * concepto.precio_unitario_subcontrato;
                }
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        if(moment(this.fecha_final).format('YYYY/MM/DD') < moment(this.fecha_inicial).format('YYYY/MM/DD'))
                        {
                            swal('¡Error!', 'La fecha de inicio no puede ser posterior a la fecha de término.', 'error')
                        }
                        else {
                            this.store()
                        }
                    }
                });
            },
            store() {
                return this.$store.dispatch('contratos/avance-subcontrato/store', {
                    id_antecedente: this.id,
                    fecha:  moment(this.fecha).format('YYYY-MM-DD'),
                    cumplimiento:  moment(this.fecha_inicial).format('YYYY-MM-DD'),
                    vencimiento:  moment(this.fecha_final).format('YYYY-MM-DD'),
                    fecha_ejecucion: moment(this.fecha_ejecucion).format('YYYY-MM-DD'),
                    fecha_contable:  moment(this.fecha_contable).format('YYYY-MM-DD'),
                    conceptos: this.conceptos,
                    observaciones: this.observaciones
                }).then(data=> {
                   // this.salir();
                })
            },
        },
        watch: {
            subtotal(value) {
                if (value) {
                    this.iva = this.subtotal * 0.16;
                    this.total = this.subtotal + this.iva;
                }
            }
        },
    }
</script>

<style scoped>
    .encabezado{
        text-align: center; background-color: #f2f4f5
    }
    table tbody .numerico
    {
        text-align: right;
        padding-left: 0;
        white-space: normal;
    }
    td.editable-cell, td.editable-cell input{
        background-color: #d0dcd0;
    }
</style>
