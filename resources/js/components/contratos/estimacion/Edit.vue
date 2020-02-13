<template>
    <span>
        <div class="card">
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
        <div class="card" v-if="estimacion">
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
					<tbody>
					<tr v-for="(concepto, i) in conceptos" :data-iddestino="concepto.para_estimar ? concepto.destino ? concepto.destino.id_destino : null : null">
						<td :title="concepto.clave">{{ concepto.clave }}</td>
						<td :title="concepto.descripcion">
							<span v-if="concepto.para_estimar == '1'">
								-- {{ concepto.descripcion }}
							</span>
							<span v-else>
                                <span v-if="i==1">- </span>
								<b>{{ concepto.descripcion}}</b>
							</span>
						</td>
						<td class="centrado">{{ concepto.unidad }}</td>

                       <td v-if="concepto.para_estimar == '1'" v-for="partida_subcontrato in partidas_subcontrato" style="display: none" class="numerico contratado">
                            {{ partida_subcontrato.id_concepto == concepto.id_concepto ? partida_subcontrato.cantidad_format : '' }}
<!--						   <td v-if="partida_subcontrato.id_concepto == concepto.id_concepto"  style="display: none" class="numerico contratado">{{ partida_subcontrato.precio_unitario_format }}</td>-->
                       </td>
                    </tr>

<!--						<td style="display: none" class="numerico avance-volumen">{{ concepto.para_estimar == '1' ? parseFloat(concepto.CantidadEstimadaTotal).formatMoney(2) : '' }}</td>-->
<!--						<td style="display: none" class="numerico avance-volumen">{{ concepto.para_estimar == '1' ? parseFloat(concepto.PctAvance).formatMoney(2) : '' }}</td>-->
<!--						<td style="display: none" class="numerico avance-importe"></td>-->
<!--						<td style="display: none" class="numerico avance-importe">{{ concepto.para_estimar == '1' ? parseFloat(concepto.MontoEstimadoTotal).formatMoney(2) : '' }}</td>-->
<!--						<td style="display: none" class="numerico saldo">{{ concepto.para_estimar == '1' ? parseFloat(concepto.CantidadSaldo).formatMoney(2) : '' }}</td>-->
<!--						<td style="display: none" class="numerico saldo">{{ concepto.para_estimar == '1' ? parseFloat(concepto.MontoSaldo).formatMoney(2) : '' }}</td>-->
<!--						<td class="editable-cell numerico">-->
<!--							<input v-on:change="changeCantidad(concepto)" class="text" v-if="concepto.para_estimar == '1'" v-model="concepto.CantidadEstimada"-->
<!--                                   :name="'CantidadEstimada' + i"-->
<!--                                   v-validate="{max_value: parseFloat(concepto.CantidadSaldo)}"-->
<!--                                   :class="{'is-invalid': errors.has('CantidadEstimada' + i)}"-->
<!--                            >-->
<!--							<p v-else></p></td>-->
<!--						<td class="editable-cell numerico">-->
<!--							<input v-on:change="changePorcentaje(concepto)" class="text" v-if="concepto.para_estimar == '1'" v-model="concepto.PctEstimado"-->

<!--                            >-->
<!--							<p v-else>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p></td>-->
<!--						<td class="numerico">{{ concepto.para_estimar == '1' ? parseFloat(concepto.PrecioUnitario).formatMoney(4) : '' }}</td>-->
<!--						<td class="editable-cell numerico">-->
<!--							<input v-on:change="changeImporte(concepto)" class="text" v-if="concepto.para_estimar == '1'" v-model="concepto.ImporteEstimado"-->

<!--                            >-->
<!--							<p v-else></p></td>-->
<!--						<td style="display: none" class="destino" :title="concepto.RutaDestino">{{ concepto.RutaDestino }}</td>-->

					</tbody>
				</table>
			</div>
        </div>


    </span>
</template>


<script>


import DeductivaEdit from './deductivas/Edit'
    export default {
        name: "estimacion-edit",
        components: {DeductivaEdit},
        data() {
            return {
                cargando: true,
                conceptos: null,
                columnas: [],
                partidas_subcontrato : null,
                partidas_estimacion : null
            }
        },
        mounted() {
            this.cargando = true;
            this.obra = this.$session.get('obra');
            this.id = this.$route.params.id;
            this.find();
        },
        methods: {
            find() {
                this.$store.commit('contratos/estimacion/SET_ESTIMACION', null);
                return this.$store.dispatch('contratos/estimacion/find', {
                    id: this.id,
                    params: {include : ['subcontrato.partidas', 'partidas']}
                }).then(data => {
                    this.$store.commit('contratos/estimacion/SET_ESTIMACION', data);
                    this.partidas_estimacion = data.partidas.data;
                    this.partidas_subcontrato = data.subcontrato.partidas.data;
                    this.cargando = false;
                    this.getConceptos();
                })
            },
            getConceptos()
            {
                return this.$store.dispatch('contratos/contrato-concepto/index', {
                    params: {
                        include : ['destino'],
                        scope: 'conceptosEstimacionOrdenado:' + this.estimacion.subcontrato.id,
                        sort: 'nivel', order: 'asc'
                    }
                })
                    .then(data => {
                       this.conceptos = data
                    })
            },

        },
        computed: {
            estimacion() {
                return this.$store.getters['contratos/estimacion/currentEstimacion']
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
            conceptos: {
                handler() {
                    setTimeout(() => {
                        this.$validator.validate()
                    }, 500);
                },
                deep: true
            }
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
