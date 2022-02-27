<template>
    <span>
        <div class="row" v-if="solicitud">
             <div class="col-12">
                 <div class="row">
                     <div class="p-2" v-if="solicitud.estado == 0">
                         <retencion-i-v-a v-bind:id="id" v-bind:base_b64="base_b64" />
                     </div>
                     <div class="p-2" v-if="solicitud.estado == 0">
                         <resumen v-bind:id="id" v-bind:base_b64="base_b64" />
                     </div>
                 </div>
                 <div class="row">
                     <div class="col-md-12">
                         <encabezado v-bind:estimacion="solicitud" />
                     </div>
                 </div>
             </div>
        </div>
        <div class="row" v-if="solicitud">
            <div class="col-md-6">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title">Periodo de Solicitud</h6>
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label class="col-form-label">Inicio</label>
                                        {{solicitud.fecha_inicio}}
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="col-form-label">Término</label>
                                        {{solicitud.fecha_fin}}
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
					<tbody v-for="(concepto, i) in partidas">
					    <tr>
						    <td :title="concepto.clave">{{ concepto.clave }}</td>
                            <td :title="concepto.descripcion_concepto">
                                <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                {{concepto.descripcion_concepto}}
                            </td>
                            <td class="centrado">{{concepto.unidad}}</td>
                            <td style="display: none" class="numerico contratado">{{ parseFloat(concepto.cantidad_subcontrato).formatMoney(2) }}</td>
                            <td style="display: none" class="numerico contratado">${{ parseFloat(concepto.precio_unitario_subcontrato).formatMoney(2) }}</td>
                            <td style="display: none" class="numerico avance-volumen"></td>
                            <td style="display: none" class="numerico avance-volumen">{{ parseFloat(concepto.cantidad_estimada_anterior).formatMoney(2) }}</td>
                            <td style="display: none" class="numerico avance-volumen">{{ parseFloat(concepto.porcentaje_avance).formatMoney(2) }}</td>
                            <td style="display: none" class="numerico avance-importe"></td>
                            <td style="display: none" class="numerico avance-importe">${{ parseFloat(concepto.importe_estimado_anterior).formatMoney(2) }}</td>
                            <td style="display: none" class="numerico saldo">{{  parseFloat(concepto.cantidad_por_estimar).formatMoney(2) }}</td>
                            <td style="display: none" class="numerico saldo">${{ parseFloat(concepto.importe_por_estimar).formatMoney(2) }}</td>
                            <td class="numerico">{{parseFloat(concepto.cantidad_estimacion).formatMoney(2)}}</td>
                            <td class="numerico">{{parseFloat(concepto.porcentaje_estimado).formatMoney(2, '.', ',')}}</td>
                            <td class="numerico">{{ concepto.precio_unitario_subcontrato_format }}</td>
                            <td class="numerico">${{parseFloat(concepto.importe_estimacion).formatMoney(2, '.', ',')}}</td>
                            <td style="display: none" class="destino" :title="concepto.destino_path">{{ concepto.destino_path }}</td>
                        </tr>
                    </tbody>
				</table>
			</div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" v-on:click="salir">
                    <i class="fa fa-angle-left"></i>Regresar
                </button>
                <button type="submit" class="btn btn-primary" @click="update" :disabled="errors.count() > 0"><i class="fa fa-save"></i>Guardar </button>
            </div>
        </div>
     </span>
</template>

<script>
    import RetencionIVA from "./RetencionIVA";
    import Resumen from "./Resumen";
    import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    import Encabezado from './partials/EncabezadoSolicitud';
    export default {
        name: "solicitud-edit-layout",
        components: {RetencionIVA, Datepicker, es, Resumen, Encabezado},
        props: ['id', 'base_b64', 'solicitud'],
        data() {
            return {
                cargando: true,
                es:es,
                columnas: [],
                base : ''
            }
        },
        mounted() {
            this.init()
            this.base = atob(this.base_b64)
        },
        methods: {
            init() {
                if(this.solicitud == undefined)
                {
                    this.salir()
                }else {
                    this.partidas = this.solicitud.partidas
                    this.cargando = false;
                }
            },
            update() {
                this.solicitud.base =  this.base;
                return this.$store.dispatch('portalProveedor/solicitud-autorizacion-avance/update', {
                    id: this.id,
                    data: this.solicitud
                })
                    .then((data) => {
                        this.$router.push({name: 'solicitud-autorizacion-avance'});
                    })
            },
            salir() {
                this.$router.push({name: 'solicitud-autorizacion-avance'});
            },
        },
        computed: {
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
            partidas: {
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
