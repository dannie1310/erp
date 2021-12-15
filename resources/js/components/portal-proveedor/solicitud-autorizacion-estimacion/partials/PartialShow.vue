<template>
    <span>
        <div class="row" v-if="cargando">
            <div class="row" >
                <div class="col-md-12">
                    <div class="spinner-border text-success" role="status">
                       <span class="sr-only">Cargando...</span>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="!cargando">
            <div class="row" >
                <div class="col-md-12" v-if="estimacion">
                    <resumen v-bind:estimacion="estimacion" v-bind:base="base" />
                </div>
            </div>
            <div class="row" >
                <div class="col-md-12" >
                    <datos v-bind:estimacion="estimacion" />
                </div>
            </div>
            <div class="row" >
                <div class="col-md-12">
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
                </div>
            </div>
            <div class="col-md-12">
                <div class="row">
                    <div class="table-responsive">
                        <table id="tabla-conceptos">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="c120">Clave</th>
                                    <th rowspan="2">Concepto</th>
                                    <th rowspan="2" class="c100">Unidad</th>
                                    <th style="display: none" colspan="2" class="contratado">Contratado</th>
                                    <th style="display: none" colspan="3" class="avance-volumen">Avance Volumen</th>
                                    <th style="display: none" colspan="2" class="avance-importe">Avance Importe</th>
                                    <th style="display: none" colspan="2" class="saldo">Saldo</th>
                                    <th colspan="4">Esta Solicitud</th>
                                </tr>
                                <tr>
                                    <th style="display: none" class="contratado c100">Volumen</th>
                                    <th style="display: none" class="contratado c100">P.U.</th>
                                    <th style="display: none" class="avance-volumen c100">Anterior</th>
                                    <th style="display: none" class="avance-volumen c100">Acumulado</th>
                                    <th style="display: none" class="avance-volumen c100">%</th>
                                    <th style="display: none" class="avance-importe c100">Anterior</th>
                                    <th style="display: none" class="avance-importe c100">Acumulado</th>
                                    <th style="display: none" class="saldo c100">Volumen</th>
                                    <th style="display: none" class="saldo c100">Importe</th>
                                    <th class="c100">Volumen</th>
                                    <th class="c100">%</th>
                                    <th class="c100">P.U.</th>
                                    <th class="c100">Importe</th>
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
                                    <td class="numerico">${{parseFloat(concepto.importe_estimacion).formatMoney(2)}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import Datos from "./DatosSolicitud";
    import Resumen from "../Resumen"
    export default {
        name: "estimacion-show",
        props: ["id", "base_b64"],
        components: {Datos, Resumen},
        data() {
            return {
                cargando: true,
                columnas: [],
                estimacion: [],
                base:''
            };
        },
        mounted() {
            if(this.base == undefined)
            {
                this.salir();
            }else {
                this.base = atob(this.base_b64)
                this.find();
            }
        },
        methods: {
            find() {
                return this.$store.dispatch('portalProveedor/solicitud-autorizacion-avance/ordenarConceptos', {
                    id: this.id,
                    base: this.base
                }).then(data => {
                    this.estimacion = data
                    this.$emit('cargaFinalizada', this.estimacion);
                }).finally(() => {
                    this.cargando = false;
                })
            },
            salir() {
                this.$router.push({name: 'solicitud-autorizacion-avance'});
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
    font-size: 13px;
}

table thead th
{
    padding: 0.2em;
    border: 1px solid #ccc;
    background-color: #f2f4f5;
    color: #242424;
    /*border-right: 1px solid #ccc;*/
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
td.editable-cell, td.editable-cell input{
    background-color: #d0dcd0;
}

.vdp-datepicker {
    padding: 0.2rem;
}
</style>
