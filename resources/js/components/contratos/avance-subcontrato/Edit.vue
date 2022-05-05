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
                <encabezado v-bind:avance="this.avance" />
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <tr>
                            <th class="encabezado" colspan="6">
                                {{avance.razon_social}}
                            </th>
                        </tr>
                        <tr>
                            <th class="encabezado  c130">
                                Folio del Subcontrato
                            </th>
                            <th class="encabezado">
                                Subcontrato
                            </th>
                            <th class="encabezado c100">Subtotal</th>
                            <th class="encabezado c100">IVA</th>
                            <th class="encabezado c100">Total</th>
                        </tr>
                        <tr>
                            <td style="text-align: center">
                                <span style="color:black; text-decoration: underline">{{avance.subcontrato.numero_folio_format}}</span>
                            </td>
                            <td style="text-align: center">
                                {{avance.subcontrato.referencia}}
                            </td>
                            <td class="money">
                                {{avance.subtotal_format}}
                            </td>
                            <td class="money">
                                {{avance.impuesto_format}}
                            </td>
                            <td class="money">
                                {{avance.total_format}}
                            </td>
                        </tr>
                    </table>
                </div>
                <hr>
                <br>
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <h6><b>Periodo de avance</b></h6>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-2">
                                <h7>Inicio:</h7>
                            </div>
                            <div class="col-md-4">
                                <datepicker v-model = "avance.cumplimiento"
                                            name = "cumplimiento"
                                            :format = "formatoFecha"
                                            :language = "es"
                                            :bootstrap-styling = "true"
                                            class = "form-control"
                                            v-validate="{required: true}"
                                            :class="{'is-invalid': errors.has('cumplimiento')}"
                                ></datepicker>
                                <div class="invalid-feedback" v-show="errors.has('cumplimiento')">{{ errors.first('cumplimiento') }}</div>
                            </div>
                            <div class="col-md-2">
                                <h7>Término:</h7>
                            </div>
                            <div class="col-md-4">
                                <datepicker v-model = "avance.vencimiento"
                                            name = "vencimiento"
                                            :format = "formatoFecha"
                                            :language = "es"
                                            :bootstrap-styling = "true"
                                            class = "form-control"
                                            v-validate="{required: true}"
                                            :class="{'is-invalid': errors.has('vencimiento')}"
                                ></datepicker>
                                <div class="invalid-feedback" v-show="errors.has('vencimiento')">{{ errors.first('vencimiento') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <h6><b>Fechas de Reconocimiento de Avance</b></h6>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-2">
                                <h7>Ejecución:</h7>
                            </div>
                            <div class="col-md-4">
                                <datepicker v-model = "avance.fecha_ejecucion"
                                            name = "fecha_ejecucion"
                                            :format = "formatoFecha"
                                            :language = "es"
                                            :bootstrap-styling = "true"
                                            class = "form-control"
                                            v-validate="{required: true}"
                                            :class="{'is-invalid': errors.has('fecha_ejecucion')}"
                                ></datepicker>
                                <div class="invalid-feedback" v-show="errors.has('fecha_ejecucion')">{{ errors.first('fecha_ejecucion') }}</div>
                            </div>
                            <div class="col-md-2">
                                <h7>Contable:</h7>
                            </div>
                            <div class="col-md-4">
                               <datepicker v-model = "avance.fecha_contable"
                                           name = "fecha_contable"
                                           :format = "formatoFecha"
                                           :language = "es"
                                           :bootstrap-styling = "true"
                                           class = "form-control"
                                           v-validate="{required: true}"
                                           :class="{'is-invalid': errors.has('fecha_contable')}"
                               ></datepicker>
                               <div class="invalid-feedback" v-show="errors.has('fecha_contable')">{{ errors.first('fecha_contable') }}</div>
                            </div>
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
                                    <td>
                                        <input class="text"
                                               style="text-align: right"
                                               v-model="concepto.cantidad_avance"
                                               :name="`cantidadAvance[${i}]`"
                                               data-vv-as="'Cantidad'"
                                               v-validate="{max_value: parseFloat(concepto.cantidad_subcontrato).toFixed(4) }"
                                               :class="{'is-invalid': errors.has(`cantidadAvance[${i}]`)}" />
                                         <div class="invalid-feedback" v-show="errors.has(`cantidadAvance[${i}]`)">{{ errors.first(`cantidadAvance[${i}]`) }}</div>
                                    </td>
                                </tr>
                            </tbody>
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
                                v-model="avance.observaciones"
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
                    <button type="button" @click="validate" :disabled="avance == ''" class="btn btn-primary">
                        <i class="fa fa-save"></i>
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import Encabezado from './partials/Encabezado';
    import datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    export default {
        name: "Edit",
        props: ['id'],
        components: {Encabezado, datepicker, es},
        data() {
            return {
                cargando: false,
                es: es,
                avance: null,
                fechasDeshabilitadas:{},
                conceptos: null
            }
        },
        mounted() {
            this.find();
        },
        methods : {
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
            salir() {
                this.$router.push({name: 'avance-subcontrato'});
            },
            find() {
                this.cargando = true;
                return this.$store.dispatch('contratos/avance-subcontrato/obtenerAvance', {
                    id: this.id,
                    params:{include: []}
                }).then(data => {
                    this.avance = data
                    this.getConceptos();
                })
            },
            getConceptos() {
                this.conceptos = null;
                this.fechasDeshabilitadas.from= new Date();
                this.$store.dispatch('contratos/subcontrato/ordenarConceptosAvance', {
                    id: this.id
                }).then(data => {
                    this.conceptos = data.partidas;
                }).finally(() => {
                    this.cargando = false;
                })
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        if(moment(this.vencimiento).format('YYYY/MM/DD') < moment(this.cumplimiento).format('YYYY/MM/DD'))
                        {
                            swal('¡Error!', 'La fecha de inicio no puede ser posterior a la fecha de término.', 'error')
                        }
                        else if(moment(this.fecha_ejecucion).format('YYYY/MM/DD') < moment(this.cumplimiento).format('YYYY/MM/DD'))
                        {
                            swal('¡Error!', 'La fecha de inicio no puede ser posterior a la fecha de ejecución.', 'error')
                        }
                        else if(moment(this.fecha_contable).format('YYYY/MM/DD') < moment(this.cumplimiento).format('YYYY/MM/DD'))
                        {
                            swal('¡Error!', 'La fecha de inicio no puede ser posterior a la fecha contable.', 'error')
                        }
                        else {
                            this.editar()
                        }
                    }
                });
            },
            editar() {
                let data = {
                    cumplimiento:  moment(this.avance.cumplimiento).format('YYYY-MM-DD'),
                    vencimiento:  moment(this.avance.vencimiento).format('YYYY-MM-DD'),
                    fecha_ejecucion: moment(this.avance.fecha_ejecucion).format('YYYY-MM-DD'),
                    fecha_contable:  moment(this.avance.fecha_contable).format('YYYY-MM-DD'),
                    conceptos: this.conceptos,
                    observaciones: this.avance.observaciones
                };
                return this.$store.dispatch('contratos/avance-subcontrato/update', {
                    id: this.id,
                    data: data,
                }).then(data=> {
                    this.salir();
                })
            },
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
