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
    <span v-if="!cargando">
        <div class="d-flex flex-row-reverse">
            <div class="p-2">
                <Resumen v-bind:id="id"></Resumen>
            </div>
        </div>
        <div class="row" v-if="!cargando">
            <div class="col-md-6">
				<div class="card">
                    <div class="card-header">
						<h6 class="card-title">Subcontrato</h6>
					</div>
					<div class="card-body">
						<form>
							<div class="form-group row">
								<label class="col-md-3 col-form-label">Fecha de la Estimación</label>
								<div class="col-md-9">
                                   {{estimacion.fecha}}
								</div>
							</div>
                             <div class="form-group row">
								<label class="col-md-3 col-form-label">Folio de la Estimación</label>
								<div class="col-md-9">
                                    {{estimacion.folio}}
                                </div>
                            </div>
                            <div class="form-group row">
								<label class="col-md-3 col-form-label">Folio Consecutivo</label>
								<div class="col-md-9">
                                    {{estimacion.folio_consecutivo}}
                                </div>
                            </div>
							<div class="form-group row">
								<label class="col-md-3 col-form-label">Objeto</label>
								<div class="col-md-9">
									({{estimacion.subcontrato.folio}}) {{ estimacion.subcontrato.referencia }}
								</div>
							</div>
							<div class="form-group row">
								<label class="col-md-3 col-form-label">Contratista</label>
								<div class="col-md-9">
									{{ estimacion.razon_social }}
								</div>
							</div>
							<div class="form-group row">
								<label class="col-md-3 col-form-label">Observaciones</label>
								<div class="col-md-9">
									{{estimacion.observaciones}}
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
            <div class="col-md-6">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title">Periodo de Estimación</h6>
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label class="col-form-label">Inicio</label>
                                         <input
                                             style="text-align:right;"
                                             :disabled="true"
                                             type="text"
                                             data-vv-as="Subtotal"
                                             class="form-control"
                                             placeholder="Subtotal"
                                             v-model="estimacion.fecha_inicial" />
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="col-form-label">Término</label>
                                        <input
                                            style="text-align:right;"
                                            :disabled="true"
                                            type="text"
                                            data-vv-as="Subtotal"
                                            class="form-control"
                                            placeholder="Subtotal"
                                            v-model="estimacion.fecha_final" />
                                    </div>
                                </div>
                            </form>
                        </div>
				    </div>
			    </div>
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title">Totales</h6>
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="form-group row">
                                    <label class="col-md-5 col-form-label">Subtotal:</label>
                                    <div class="col-md-7">
                                        <input
                                            style="text-align:right;"
                                            :disabled="true"
                                            type="text"
                                            data-vv-as="Subtotal"
                                            class="form-control"
                                            placeholder="Subtotal"
                                            v-model="parseFloat(estimacion.subtotal).formatMoney(2)" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-5 col-form-label">IVA:</label>
                                    <div class="col-md-7">
                                         <input
                                             style="text-align:right;"
                                             :disabled="true"
                                             type="text"
                                             data-vv-as="IVA"
                                             class="form-control"
                                             placeholder="IVA"
                                             v-model="parseFloat(estimacion.iva).formatMoney(2)" />
                                    </div>
                                </div>
                                <div class="form-group row" v-if="estimacion.retencion_iva_monto > 0">
                                    <label class="col-md-5 col-form-label">IVA Retenido ({{estimacion.retencion_iva_tasa}}):</label>
                                    <div class="col-md-7">
                                         <input
                                             style="text-align:right;"
                                             :disabled="true"
                                             type="text"
                                             data-vv-as="IVA"
                                             class="form-control"
                                             placeholder="IVA"
                                             v-model="parseFloat(estimacion.retencion_iva_monto).formatMoney(2)" />
                                    </div>
                                </div>
                                <div class="form-group row" v-if="estimacion.retencion_isr_monto > 0">
                                    <label class="col-md-5 col-form-label">ISR Retenido ({{estimacion.retencion_isr_tasa}}):</label>
                                    <div class="col-md-7">
                                         <input
                                             style="text-align:right;"
                                             :disabled="true"
                                             type="text"
                                             data-vv-as="IVA"
                                             class="form-control"
                                             placeholder="IVA"
                                             v-model="parseFloat(estimacion.retencion_isr_monto).formatMoney(2)" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-5 col-form-label">Total:</label>
                                    <div class="col-md-7">
                                         <input
                                             style="text-align:right;"
                                             :disabled="true"
                                             type="text"
                                             data-vv-as="total"
                                             class="form-control"
                                             placeholder="total"
                                             v-model="parseFloat(estimacion.total).formatMoney(2)" />
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
		</div>
        <div class="card" v-if="!cargando">
			<div class="card-body">
				<div class="form-check form-check-inline">
					<input v-model="columnas" class="form-check-input" type="checkbox" value="contratado" id="contratado">
					<label class="form-check-label" for="contratado">Contratado</label>
				</div>
				<div class="form-check form-check-inline">
					<input v-model="columnas" class="form-check-input" type="checkbox" id="avance-volumen" value="avance-volumen">
					<label class="form-check-label" for="avance-volumen">Avance Volumen</label>
				</div>
				<div class="form-check form-check-inline">
					<input v-model="columnas" class="form-check-input" type="checkbox" id="avance-importe" value="avance-importe">
					<label class="form-check-label" for="avance-importe">Avance Importe</label>
				</div>
				<div class="form-check form-check-inline">
					<input v-model="columnas" class="form-check-input" type="checkbox" id="saldo" value="saldo">
					<label class="form-check-label" for="saldo">Saldo</label>
				</div>
				<div class="form-check form-check-inline">
					<input v-model="columnas" class="form-check-input" type="checkbox" id="destino" value="destino">
					<label class="form-check-label" for="destino">Destino</label>
				</div>
			</div>
		</div>
        <div class="card" v-if="!cargando">
			<div class="card-body table-responsive">
				<table id="tabla-conceptos">
					<thead>
						<tr>
							<th rowspan="2">Clave</th>
							<th rowspan="2">Concepto</th>
							<th rowspan="2">UM</th>
							<th style="display: none" colspan="2" class="contratado">Contratado</th>
							<th style="display: none" colspan="3" class="avance-volumen">Avance Volumen</th>
							<th style="display: none" colspan="2" class="avance-importe">Avance Importe</th>
							<th style="display: none" colspan="2" class="saldo">Saldo</th>
							<th colspan="4">Esta Estimación</th>
							<th style="display: none" class="destino">Distribución</th>
						</tr>
						<tr>
							<th style="display: none" class="contratado">Volumen</th>
							<th style="display: none" class="contratado">P.U.</th>
							<th style="display: none" class="avance-volumen">Anterior</th>
							<th style="display: none" class="avance-volumen">Acumulado</th>
							<th style="display: none" class="avance-volumen">%</th>
							<th style="display: none" class="avance-importe">Anterior</th>
							<th style="display: none" class="avance-importe">Acumulado</th>
							<th style="display: none" class="saldo">Volumen</th>
							<th style="display: none" class="saldo">Importe</th>
							<th>Volumen</th>
							<th>%</th>
							<th>P.U.</th>
							<th>Importe</th>
							<th style="display: none" class="destino">Destino</th>
						</tr>
					</thead>
					<tbody v-for="(concepto, i) in estimacion.subcontrato.partidas">
                        <tr v-if="concepto.para_estimar == 0">
                            <td :title="concepto.clave"><b>{{concepto.clave}}</b></td>
                            <td :title="concepto.descripcion">
                                <span v-for="n in concepto.nivel">&nbsp;</span>
                                <b>{{concepto.descripcion}}</b></td>
                            <td></td>
                            <td style="display: none" class="numerico contratado"/>
                            <td style="display: none" class="numerico contratado"/>
                            <td style="display: none" class="numerico avance-volumen"/>
                            <td style="display: none" class="numerico avance-volumen"/>
                            <td style="display: none" class="numerico avance-volumen"/>
                            <td style="display: none" class="numerico avance-importe"/>
                            <td style="display: none" class="numerico avance-importe"/>
                            <td style="display: none" class="numerico saldo"/>
                            <td style="display: none" class="numerico saldo"/>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="display: none" class="destino"/>
                        </tr>
					    <tr v-else>
						    <td :title="concepto.clave">{{ concepto.clave }}</td>
                            <td :title="concepto.descripcion_concepto">
                                <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                {{concepto.descripcion_concepto}}
                            </td>
                            <td class="centrado">{{concepto.unidad}}</td>
                            <td style="display: none" class="numerico contratado">{{ parseFloat(concepto.cantidad_subcontrato).formatMoney(2) }}</td>
                            <td style="display: none" class="numerico contratado">{{ parseFloat(concepto.precio_unitario_subcontrato).formatMoney(2) }}</td>
                            <td style="display: none" class="numerico avance-volumen"></td>
                            <td style="display: none" class="numerico avance-volumen">{{ parseFloat(concepto.cantidad_estimada_anterior).formatMoney(2) }}</td>
                            <td style="display: none" class="numerico avance-volumen">{{ parseFloat(concepto.porcentaje_avance).formatMoney(2) }}</td>
                            <td style="display: none" class="numerico avance-importe"></td>
                            <td style="display: none" class="numerico avance-importe">{{ parseFloat(concepto.importe_estimado_anterior).formatMoney(2) }}</td>
                            <td style="display: none" class="numerico saldo">{{  parseFloat(concepto.cantidad_por_estimar).formatMoney(2) }}</td>
                            <td style="display: none" class="numerico saldo">{{ parseFloat(concepto.importe_por_estimar).formatMoney(2) }}</td>
                            <td class="numerico">{{parseFloat(concepto.cantidad_estimacion).formatMoney(2)}}</td>
                            <td class="numerico">{{parseFloat(concepto.porcentaje_estimado).formatMoney(2)}}</td>
                            <td class="numerico">{{ concepto.precio_unitario_subcontrato_format}}</td>
                            <td class="numerico">{{parseFloat(concepto.importe_estimacion).formatMoney(2)}}</td>
                            <td style="display: none" class="destino" :title="concepto.destino_path">{{ concepto.destino_path }}</td>
                        </tr>
                    </tbody>
				</table>
			</div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" v-on:click="salir">Cerrar</button>
            </div>
        </div>
    </span>
    </span>
</template>

<script>
    import Resumen from './resumen/Show';
    export default {
        name: "estimacion-show",
        props: ["id"],
        components: {Resumen},
        data() {
            return {
                cargando: true,
                columnas: [],
                estimacion: [],
            };
        },
        mounted() {
            this.find();
        },
        methods: {
            find() {
                return this.$store.dispatch('contratos/estimacion/ordenarConceptos', {
                    id: this.id,
                    params: {}
                }).then(data => {
                    this.estimacion = data;
                }).finally(() => {
                    this.cargando = false;
                })
            },
            salir() {
                this.$router.push({name: 'estimacion'});
            },
        },
        watch: {
            columnas(val) {
                $('.contratado').css('display', 'none');
                $('.avance-volumen').css('display', 'none');
                $('.avance-importe').css('display', 'none');
                $('.saldo').css('display', 'none');
                $('.destino').css('display', 'none');

                val.forEach(v => {
                    $('.' + v).removeAttr('style')
                })
            },
        }
    }
</script>

<style scoped>
    table#tabla-conceptos {
        word-wrap: unset;
        width: 100%;
        background-color: white;
        border-color: transparent;
        border-collapse: collapse;
        clear: both;
    }

    table thead th
    {
        padding: 0.2em;
        border: 1px solid #666;
        background-color: #333;
        color: white;
        font-weight: normal;
        overflow: hidden;
        text-align: center;
    }

    table thead th {
        text-align: center;
    }
    table tbody tr
    {
        border-width: 0 1px 1px 1px;
        border-style: none solid solid solid;
        border-color: white #CCCCCC #CCCCCC #CCCCCC;
    }
    table tbody td,
    table tbody th
    {
        border-right: 1px solid #ccc;
        color: #242424;
        line-height: 20px;
        overflow: hidden;
        padding: 1px 5px;
        text-align: left;
        text-overflow: ellipsis;
        -o-text-overflow: ellipsis;
        -ms-text-overflow: ellipsis;
        white-space: nowrap;
    }

    table col.clave { width: 120px; }
    table col.icon { width: 25px; }
    table col.monto { width: 115px; }
    table col.pct { width: 60px; }
    table col.unidad { width: 80px; }
    table col.clave  {width: 100px; }

    table tbody td input.text
    {
        border: none;
        padding: 0;
        margin: 0;
        width: 100%;
        background-color: transparent;
        font-family: inherit;
        font-size: inherit;
        font-weight: bold;
    }

    table tbody .numerico
    {
        text-align: right;
        padding-left: 0;
        white-space: normal;
    }

    .text.is-invalid {
        color: #dc3545;
    }

    table tbody td input.text {
        text-align: right;
    }
</style>
