<template>
    <span>
        <div class="card" v-if="!avance">
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
                <div class="row  justify-content-end">
                    <div class="col-md-2">
                    <div class="card">
                        <div class="card-header">
                            <h5><b>Folio:</b> {{avance.numero_folio_format}}</h5>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <tr>
                                    <th class="encabezado" colspan="11">
                                        {{avance.concepto_descripcion}}
                                    </th>
                                </tr>
                                <tr>
                                    <th class="encabezado">
                                        Fecha
                                    </th>
                                    <th class="encabezado" colspan="4">
                                        Periodo de Avance
                                    </th>
                                    <th class="encabezado">Registró</th>
                                    <th class="encabezado">
                                        Estado
                                    </th>
                                    <th class="encabezado" colspan="4">
                                        Totales
                                    </th>
                                </tr>
                                <tr>
                                    <td rowspan="2">
                                        {{avance.fecha_format}}
                                    </td>
                                    <th class="encabezado" rowspan="2">Inicio:</th>
                                    <td rowspan="2">{{avance.cumplimiento_format}}</td>
                                    <th class="encabezado" rowspan="2">Término:</th>
                                    <td rowspan="2">
                                        {{avance.vencimiento_format}}
                                    </td>
                                    <td rowspan="2" style="text-align: center">{{avance.nombre_usuario}}</td>
                                    <td style="text-align: center" rowspan="2">
                                        <estado v-bind:value="{color: avance.color_estado, descripcion: avance.descripcion_estado}" />
                                    </td>
                                    <th class="encabezado">Subtotal:</th>
                                    <td>{{avance.subtotal_format}}</td>
                                    <th class="encabezado">IVA:</th>
                                    <td>{{avance.impuesto_format}}</td>
                                </tr>
                                <tr>
                                    <th class="encabezado">Total:</th>
                                    <td colspan="3">{{avance.total_format}}</td>
                                </tr>
                                <template v-if="avance.observaciones!=''">
                                    <tr>
                                        <th colspan="11" class="encabezado">
                                            Observaciones
                                        </th>
                                    </tr>
                                    <tr>
                                        <td colspan="11">
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
                                        <td v-if="partida.concepto.medible != 3"><b>{{partida.concepto.descripcion}}</b></td>
                                        <td v-else> {{partida.concepto.descripcion}}</td>
                                        <td style="text-align:center;">{{partida.concepto.unidad}}</td>
                                        <td v-if="partida.concepto.medible == 3" style="text-align:right;">
                                            {{partida.concepto.cantidad_presupuestada_calculada}}
                                        </td>
                                        <td v-else></td>
                                        <td v-if="partida.concepto.medible == 3" style="text-align:right;">
                                            {{partida.cantidad_anterior_avance}}
                                        </td>
                                        <td v-else></td>
                                        <td v-if="partida.concepto.medible == 3">
                                            {{partida.cantidad_format}}
                                        </td>
                                        <td v-else></td>
                                        <td v-if="partida.concepto.medible == 3" style="text-align:right;">
                                            {{partida.concepto.precio_venta}}
                                        </td>
                                        <td v-else></td>
                                        <td v-if="partida.concepto.medible == 3" style="text-align:right;">
                                            {{partida.monto_avance_format}}
                                        </td>
                                        <td v-else></td>
                                        <td v-if="partida.concepto.medible == 3" style="text-align:right;">
                                            {{partida.cantidad_avance_actual_format}}
                                        </td>
                                        <td v-else></td>
                                        <td v-if="partida.concepto.medible == 3" style="text-align:right;">
                                            {{partida.monto_avance_actual_format}}
                                        </td>
                                        <td v-else></td>
                                        <td v-if="partida.concepto.medible == 3" style="text-align: center">
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
    import Estado from './partials/EstatusLabel'
    export default {
        name: "avance-obra-show",
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
