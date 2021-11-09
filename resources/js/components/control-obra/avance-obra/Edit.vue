<template>
    <span>
        <div class="card">
            <div class="card-body">
                <span v-if="!avance">
                    <div class="col-md-12">
                        <div class="spinner-border text-success" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                </span>

                <span v-else>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="offset-md-8 col-md-4">
                                    <span class="pull-right">
                                        <div class="card">
                                            <div class="card-body">
                                                <table style="font-size: 1.3em">
                                                    <tbody>
                                                        <tr>
                                                            <td colspan="2" style="border-bottom: 1px solid #9e9e9e; text-align: center">
                                                                <b>Avance de Obra</b>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Folio:</td>
                                                            <td style="text-align: right">
                                                                <b><span style="color:black; text-decoration: underline">{{avance.numero_folio_format}}</span></b>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Fecha:</td>
                                                            <td style="text-align: right">
                                                                <b>{{avance.fecha_format}}</b>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="col-md-2">
                                        <label for="fecha">Fecha:</label>
                                    </div>
                                    <div class="col-md-10">
                                        <datepicker v-model = "avance.fecha" :disabled="true"
                                                    id="fecha"
                                                    name = "fecha"
                                                    :format = "formatoFecha"
                                                    :language = "es"
                                                    :bootstrap-styling = "true"
                                                    class = "form-control"
                                                    :class="{'is-invalid': errors.has('fecha')}"
                                        ></datepicker>
                                        <div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header ui-sortable-handle" style="cursor: move;">
                                    <h3 class="card-title">
                                        <i class="fas fa-chart-pie mr-1"></i>Periodo de Avance
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content p-0">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label for="fecha">Inicio:</label>
                                            </div>
                                            <div class="col-md-4">
                                                <datepicker v-model = "avance.cumplimiento"
                                                            id="fechaInicio"
                                                            name = "fechaInicio"
                                                            :format = "formatoFecha"
                                                            :language = "es"
                                                            :bootstrap-styling = "true"
                                                            class = "form-control"
                                                            v-validate="{required: true}"
                                                            :class="{'is-invalid': errors.has('fechaInicio')}"
                                                ></datepicker>
                                                <div class="invalid-feedback" v-show="errors.has('fechaInicio')">{{ errors.first('fechaInicio') }}</div>
                                            </div>
                                            <div class="col-md-2">
                                                <label for="fechaTermino">Término:</label>
                                            </div>
                                            <div class="col-md-4">
                                                <datepicker v-model = "avance.vencimiento"
                                                            id="fechaTermino"
                                                            name = "fechaTermino"
                                                            :format = "formatoFecha"
                                                            :language = "es"
                                                            :bootstrap-styling = "true"
                                                            class = "form-control"
                                                            v-validate="{required: true}"
                                                            :class="{'is-invalid': errors.has('fechaTermino')}"
                                                ></datepicker>
                                                <div class="invalid-feedback" v-show="errors.has('fechaTermino')">{{ errors.first('fechaTermino') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr />
                    <div class="row">
                        <div class="col-md-2">
                            <label>Concepto: </label>
                        </div>
                        <div class="col-md-10">
                            <p>{{avance.concepto_descripcion}}</p>
                        </div>
                    </div>
                    <br />
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered " id="tabla-resumen-monedas">
                                    <thead>
                                        <tr>
                                            <th class="index_corto" rowspan="2">#</th>
                                            <th rowspan="2">Clave</th>
                                            <th rowspan="2">Unidad</th>
                                            <th colspan="3">Cantidad</th>
                                            <th rowspan="2">Precio Venta</th>
                                            <th rowspan="2">Monto Avance</th>
                                            <th rowspan="2">Cantidad Actual</th>
                                            <th rowspan="2">Monto Actual</th>
                                            <th rowspan="2">Cumplido</th>
                                        </tr>
                                        <tr>
                                            <th>Presupuesto</th>
                                            <th>Anterior</th>
                                            <th>Avance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(partida,i) in avance.partidas.data">
                                            <td style="text-align:center; vertical-align:inherit;">{{partida.i}}</td>
                                            <td> {{partida.concepto.descripcion}}</td>
                                            <td style="text-align:center;">{{partida.concepto.unidad}}</td>
                                            <td style="text-align:right;">{{partida.concepto.cantidad_presupuestada_calculada}}</td>
                                            <td style="text-align:right;">{{partida.cantidad_anterior_avance}}</td>
                                            <td>
                                                <input type="text"
                                                        class="form-control"
                                                        v-on:keyup="getEditarCantidades(partida)"
                                                        :name="`avance[${i}]`"
                                                        data-vv-as="Avance"
                                                        v-validate="{required: true, min_value:-(partida.concepto.cantidad_presupuestada_calculada+100), regex: /^[+-]?[0-9]\d*(\.\d{0,6})?$/}"
                                                        :class="{'is-invalid': errors.has(`avance[${i}]`)}"
                                                        v-model="partida.cantidad_format"
                                                        style="text-align: right" />
                                                <div class="invalid-feedback" v-show="errors.has(`avance[${i}]`)">{{ errors.first(`avance[${i}]`) }}</div>
                                            </td>
                                            <td style="text-align:right;">{{partida.precio_unitario_format}}</td>
                                            <td style="text-align:right;">{{partida.monto_avance_format}}</td>
                                            <td style="text-align:right;">{{partida.cantidad_avance_actual_format}}</td>
                                            <td style="text-align:right;">{{partida.monto_avance_actual_format}}</td>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="" id="cumplido" :name="`cumplido[${i}]`" v-model="partida.cumplido">
                                                    <label class="form-check-label" for="cumplido">Si</label>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <hr />
                    <div class="row">
                        <div class="col-md-2">
                            <label for="observaciones" class="col-form-label">Observaciones: </label>
                        </div>
                        <div class="col-md-10">
                            <div class="form-group row error-content">
                                <textarea
                                    name="observaciones"
                                    id="observaciones"
                                    class="form-control"
                                    v-model="avance.observaciones"
                                    v-validate="{required: true}"
                                    data-vv-as="Observaciones"
                                    :class="{'is-invalid': errors.has('observaciones')}"
                                ></textarea>
                                <div class="invalid-feedback" v-show="errors.has('observaciones')">{{ errors.first('observaciones') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" v-on:click="salir">
                            <i class="fa fa-angle-left"></i>
                            Regresar</button>
                        <button type="button" class="btn btn-primary" v-on:click="validate">
                            Continuar
                            <i class="fa fa-angle-right"></i>
                        </button>
                    </div>
                </span>
            </div>
        </div>
    </span>
</template>

<script>
    import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    export default {
        name: "avance-obra-edit",
        props: ['id'],
        components: {Datepicker, es},
        data() {
            return {
                es:es,
                fechasDeshabilitadas:{},
                cargando: false,
                avance : null,
            }
        },
        mounted() {
            this.find();
        },
        methods: {
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
            salir(){
                 this.$router.push({name: 'avance-obra'});
            },
            find(){
                this.cargando = true;
                return this.$store.dispatch('controlObra/avance-obra/find', {
                    id: this.id,
                    params:{ include: [ 'partidas.concepto' ]}
                }).then(data => {
                    this.avance = data;
                }).finally(() => {
                    this.cargando = false;
                })
            },
            getEditarCantidades(partida){
                partida.cantidad_avance_actual = parseFloat(parseFloat(partida.cantidad_anterior_avance) + parseFloat(partida.cantidad));
                partida.monto_avance_actual =    parseFloat((parseFloat(partida.cantidad_anterior_avance) + parseFloat(partida.cantidad)) * parseFloat(partida.precio_unitario));
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.edit()
                    }
                });
            },
            edit() {
                var datos = {};
                datos['id_transaccion'] = this.avance.id;
                datos['fecha'] = this.avance.fecha;
                datos['fecha_inicio'] = this.avance.cumplimiento;
                datos['fecha_termino'] = this.avance.vencimiento;
                datos['observaciones'] = this.avance.observaciones;
                datos['conceptos'] = this.avance.partidas.data;
                return this.$store.dispatch('controlObra/avance-obra/edit',{
                    id:this.id,
                    data:datos
                })
                    .then((data) => {
                        this.salir();
                    });
            },
        }
    }
</script>

<style scoped>
    .encabezado{
        text-align: center; background-color: #f2f4f5
    }
    table#tabla-resumen-monedas, table.tabla {
        word-wrap: unset;
        width: 100%;
        background-color: white;
        border-color: transparent;
        border-collapse: collapse;
        clear: both;
    }
</style>