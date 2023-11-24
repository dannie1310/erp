<template>
    <span>
        <div class="row" v-if="!avance">
            <div class="col-md-12">
                <div class="spinner-border text-success" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
            </div>
        </div>
        <div class="row" v-else>
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
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <tr>
                                    <th class="encabezado" colspan="7">
                                       [{{avance.concepto_nivel}}] {{avance.concepto_descripcion}}
                                    </th>
                                </tr>
                                <tr>
                                    <th class="encabezado" colspan="2">
                                        Periodo de Avance
                                    </th>
                                    <th class="encabezado" rowspan="2">Registró</th>
                                    <th class="encabezado" rowspan="2">
                                        Estado
                                    </th>
                                    <th class="encabezado" rowspan="2">
                                        Subtotal
                                    </th>
                                    <th class="encabezado" rowspan="2">
                                        IVA
                                    </th>
                                    <th class="encabezado" rowspan="2">
                                        Total
                                    </th>
                                </tr>
                                <tr>
                                    <th class="encabezado">Inicio</th>
                                    <th class="encabezado">Término</th>
                                </tr>
                                <tr>
                                    <td>{{avance.cumplimiento_format}}</td>
                                    <td>{{avance.vencimiento_format}}</td>
                                    <td style="text-align: center">{{avance.nombre_usuario}}</td>
                                    <td style="text-align: center">
                                        <estado v-bind:value="{color: avance.color_estado, descripcion: avance.descripcion_estado}" />
                                    </td>
                                    <td style="text-align:right;">{{avance.subtotal_format}}</td>
                                    <td style="text-align:right;">{{avance.impuesto_format}}</td>
                                    <td style="text-align:right;">{{avance.total_format}}</td>
                                </tr>
                                <template v-if="avance.observaciones!=''">
                                    <tr>
                                        <th colspan="7" class="encabezado">
                                            Observaciones
                                        </th>
                                    </tr>
                                    <tr>
                                        <td colspan="7">
                                            {{avance.observaciones}}
                                        </td>
                                    </tr>
                                </template>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered " id="tabla-resumen-monedas">
                                <thead>
                                    <tr>
                                        <th class="index_corto encabezado" rowspan="2">#</th>
                                        <th class="encabezado" rowspan="2">Clave</th>
                                        <th class="encabezado" rowspan="2">Concepto</th>
                                        <th class="encabezado" rowspan="2">Unidad</th>
                                        <th class="encabezado" colspan="3">Cantidad</th>
                                        <th class="encabezado" rowspan="2">Precio Venta</th>
                                        <th class="encabezado" rowspan="2">Monto Avance</th>
                                        <th class="encabezado" rowspan="2">Cantidad Actual</th>
                                        <th class="encabezado" rowspan="2">Monto Actual</th>
                                        <th class="encabezado" rowspan="2">Cumplido</th>
                                    </tr>
                                    <tr>
                                        <th class="encabezado">Presupuesto</th>
                                        <th class="encabezado">Anterior</th>
                                        <th class="encabezado">Avance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(partida,i) in avance.partidas.data">
                                        <td style="text-align:center; vertical-align:inherit;">{{i+1}}</td>
                                        <td> {{partida.concepto.nivel}}</td>
                                        <td v-if="partida.concepto.medible != 3"><b>{{partida.concepto.descripcion}}</b></td>
                                        <td v-else> {{partida.concepto.descripcion}}</td>
                                        <td style="text-align:center;">{{partida.concepto.unidad}}</td>
                                        <td v-if="partida.concepto.medible == 1 || partida.concepto.medible == 3" style="text-align:right;">
                                            {{partida.concepto.cantidad_presupuestada_calculada}}
                                        </td>
                                        <td v-else></td>
                                        <td v-if="partida.concepto.medible == 1 || partida.concepto.medible == 3" style="text-align:right;">
                                            {{partida.cantidad_anterior_avance}}
                                        </td>
                                        <td v-else></td>
                                        <td v-if="partida.concepto.medible == 1 || partida.concepto.medible == 3">
                                            {{partida.cantidad_format}}
                                        </td>
                                        <td v-else></td>
                                        <td v-if="partida.concepto.medible == 1 || partida.concepto.medible == 3" style="text-align:right;">
                                            {{partida.concepto.precio_venta}}
                                        </td>
                                        <td v-else></td>
                                        <td v-if="partida.concepto.medible == 1 || partida.concepto.medible == 3" style="text-align:right;">
                                            {{partida.monto_avance_format}}
                                        </td>
                                        <td v-else></td>
                                        <td v-if="partida.concepto.medible == 1 || partida.concepto.medible == 3" style="text-align:right;">
                                            {{partida.cantidad_avance_actual_format}}
                                        </td>
                                        <td v-else></td>
                                        <td v-if="partida.concepto.medible == 1 || partida.concepto.medible == 3" style="text-align:right;">
                                            {{partida.monto_avance_actual_format}}
                                        </td>
                                        <td v-else></td>
                                        <td v-if="partida.concepto.medible == 1 || partida.concepto.medible == 3" style="text-align: center">
                                            <label class="form-check-label" v-if="partida.cumplido"><b>Si</b></label>
                                            <label class="form-check-label" v-else><b>No</b></label>
                                        </td>
                                        <td v-else></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import Estado from './EstatusLabel'
    export default {
        name: "avance-obra-tabla",
        props: ['id'],
        components: {Estado},
        data() {
            return {
                cargando: false,
                avance : null,
            }
        },
        mounted() {
            this.find();
        },
        methods: {
            find()
            {
                this.cargando = true;
                return this.$store.dispatch('controlObra/avance-obra/find', {
                    id: this.id,
                    params:{ include: [ 'partidas.concepto' ]}
                }).then(data => {
                    this.avance = data;
                    this.$emit('cargaFinalizada', true);
                }).finally(() => {
                    this.cargando = false;
                })
            },
        }
    }
</script>

<style scoped>
    .encabezado{
        text-align: center; background-color: #f2f4f5
    }
</style>
