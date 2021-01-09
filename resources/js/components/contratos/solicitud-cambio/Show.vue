<template>
    <span>

        <div class="row" v-if="!cargando">
            <div class="col-md-6">
				<div class="card">
                    <div class="card-header">
						<h6 class="card-title">Solicitud de Cambio</h6>
					</div>
					<div class="card-body">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label">Folio:</label>
                                    <div class="col-md-8">
                                        {{estimacion.numero_folio_format}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label">Fecha:</label>
                                    <div class="col-md-8">
                                       {{estimacion.fecha_format}}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label">Total:</label>
                                    <div class="col-md-8">
                                        {{estimacion.monto_format}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label">Moneda:</label>
                                    <div class="col-md-8">
                                       {{estimacion.moneda.nombre}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label">Observaciones:</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            {{estimacion.observaciones}}
                        </div>
					</div>
				</div>
			</div>
            <div class="col-md-6">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title">Subcontrato</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
								<label class="col-md-3 col-form-label">Contratista:</label>
								<div class="col-md-9">
                                    {{ estimacion.empresa.razon_social }}
								</div>
							</div>
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label">Fecha:</label>
                                        <div class="col-md-9">
                                            {{ estimacion.subcontrato.fecha_format }}
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label">Folio:</label>
                                        <div class="col-md-9">
                                             {{estimacion.subcontrato.numero_folio_format}} ({{ estimacion.subcontrato.referencia }})
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label">Moneda:</label>
                                        <div class="col-md-9">
                                            {{ estimacion.subcontrato.moneda }}
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label">IVA:</label>
                                        <div class="col-md-9">
                                            {{ estimacion.subcontrato.impuesto_format }}
                                        </div>
                                    </div>
                                    <div class="form-group row" >
                                        <label class="col-md-3 col-form-label">Monto:</label>
                                        <div class="col-md-9">
                                            {{ estimacion.subcontrato.monto_format }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
		</div>
        <div class="card" v-if="!cargando" style="display:none">
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
							<th colspan="2" class="contratado">Contratado</th>
							<th colspan="2" class="avance-volumen">Avance</th>

							<th colspan="2" class="saldo">Saldo</th>
							<th colspan="3">Addendum</th>
							<th class="destino">Distribución</th>
                            <th style="width: 20px"></th>
						</tr>
						<tr>
							<th class="contratado">Volumen</th>
							<th class="contratado">P.U.</th>
							<th class="avance-volumen">Volumen</th>
							<th class="avance-importe">Importe</th>
							<th class="saldo">Volumen</th>
							<th class="saldo">Importe</th>
							<th style="width: 80px">Volumen</th>
							<th>P.U.</th>
							<th>Importe</th>
							<th class="destino">Destino</th>
                            <th></th>
						</tr>
					</thead>
					<tbody >
                        <tr v-for="(partida, i) in estimacion.partidas.data">
                            <template v-if="partida.item_subcontrato">
                                <td ><b>{{partida.item_subcontrato.contrato.clave}}</b></td>
                                <td><b>{{partida.item_subcontrato.contrato.descripcion}}</b></td>
                                <td>{{partida.item_subcontrato.contrato.unidad}}</td>
                                <td class="numerico contratado">{{partida.item_subcontrato.cantidad_format}}</td>
                                <td class="numerico contratado">{{partida.item_subcontrato.precio_unitario_format}}</td>
                                <td class="numerico avance-volumen">{{partida.item_subcontrato.cantidad_estimada_format}}</td>
                                <td class="numerico avance-volumen">{{partida.item_subcontrato.importe_estimado_format}}</td>
                                <td class="numerico avance-volumen">{{partida.item_subcontrato.cantidad_saldo_format}}</td>
                                <td class="numerico avance-volumen">{{partida.item_subcontrato.importe_saldo_format}}</td>

                            </template>
                            <template v-else>
                                <td ><b>{{partida.clave}}</b></td>
                                <td><b>{{partida.descripcion}}</b></td>
                                <td>{{partida.unidad}}</td>
                                <td class="numerico">-</td>
                                <td class="numerico">-</td>
                                <td class="numerico">-</td>
                                <td class="numerico">-</td>
                                <td class="numerico">-</td>
                                <td class="numerico">-</td>
                            </template>

                            <td class="numerico avance-importe">{{partida.cantidad_format}}</td>
                            <td class="numerico saldo">{{partida.precio_format}}</td>
                            <td class="numerico saldo">{{partida.importe_format}}</td>
                            <td class="destino" v-if="partida.item_subcontrato" :title="partida.item_subcontrato.concepto_path">{{partida.item_subcontrato.concepto_path_corta}}</td>
                            <td class="destino" v-else :title="partida.concepto_path">{{partida.concepto_path_corta}}</td>
                            <th></th>
                        </tr>
                    </tbody>
				</table>
			</div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" v-on:click="salir">Cerrar</button>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "estimacion-show",
        props: ["id"],
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
                return this.$store.dispatch('contratos/solicitud-cambio/find', {
                    id: this.id,
                    params: {
                        include: ['moneda', 'empresa', 'subcontrato', 'partidas.item_subcontrato.contrato']
                    }
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
