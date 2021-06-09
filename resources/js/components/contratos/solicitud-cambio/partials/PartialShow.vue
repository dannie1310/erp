<template>
    <span>
        <div class="spinner-border text-success" role="status" v-if="cargando">
           <span class="sr-only">Cargando...</span>
        </div>

        <div  v-if="!cargando">
            <div class="row">
                <div class="col-md-12">
                    <DatosSolicitud v-bind:solicitud_cambio="solicitud_cambio" v-if="!cargando"></DatosSolicitud>
                    <tabla-datos-subcontrato  v-bind:subcontrato="solicitud_cambio.subcontrato" v-if="!cargando"></tabla-datos-subcontrato>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 table-responsive">
                    <span>Partidas de Solicitud de Cambio</span>
                    <table id="tabla-conceptos">
                        <thead>
                            <tr>
                                <th rowspan="2" >Tipo</th>
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
                        <template  v-for="(partida, i) in solicitud_cambio.partidas.data">
                            <tr v-if="partida.tiene_hijos" style="font-weight: bold">
                                <template v-if="partida.item_subcontrato">
                                    <td >{{partida.tipo.descripcion}}</td>
                                    <td >{{partida.item_subcontrato.contrato.clave}}</td>
                                    <td ><span v-for="n in partida.nivel">&nbsp;</span>{{partida.item_subcontrato.contrato.descripcion}}</td>
                                    <td >{{partida.item_subcontrato.contrato.unidad}}</td>
                                    <td class="numerico contratado">{{partida.item_subcontrato.cantidad_format}}</td>
                                    <td class="numerico contratado">{{partida.item_subcontrato.precio_unitario_format}}</td>
                                    <td class="numerico avance-volumen">{{partida.item_subcontrato.cantidad_estimada_format}}</td>
                                    <td class="numerico avance-volumen">{{partida.item_subcontrato.importe_estimado_format}}</td>
                                    <td class="numerico avance-volumen">{{partida.item_subcontrato.cantidad_saldo_format}}</td>
                                    <td class="numerico avance-volumen">{{partida.item_subcontrato.importe_saldo_format}}</td>
                                </template>
                                <template v-else>
                                    <td >{{partida.tipo.descripcion}}</td>
                                    <td >{{partida.clave}}</td>
                                    <td ><span v-for="n in partida.nivel">&nbsp;</span>{{partida.descripcion}}</td>
                                    <td ></td>
                                    <td class="numerico"></td>
                                    <td class="numerico"></td>
                                    <td class="numerico"></td>
                                    <td class="numerico"></td>
                                    <td class="numerico"></td>
                                    <td class="numerico"></td>
                                </template>

                                <td class="numerico avance-importe" ></td>
                                <td class="numerico saldo" ></td>
                                <td class="numerico saldo" ></td>
                                <td class="destino" v-if="partida.item_subcontrato" ></td>
                                <td class="destino" v-else ></td>
                            </tr>

                            <tr v-else>
                                <template v-if="partida.item_subcontrato">
                                    <td >{{partida.tipo.descripcion}}</td>
                                    <td >{{partida.item_subcontrato.contrato.clave}}</td>
                                    <td ><span v-for="n in partida.nivel">&nbsp;</span>{{partida.item_subcontrato.contrato.descripcion}}</td>
                                    <td >{{partida.item_subcontrato.contrato.unidad}}</td>
                                    <td class="numerico contratado">{{partida.item_subcontrato.cantidad_format}}</td>
                                    <td class="numerico contratado">{{partida.item_subcontrato.precio_unitario_format}}</td>
                                    <td class="numerico avance-volumen">{{partida.item_subcontrato.cantidad_estimada_format}}</td>
                                    <td class="numerico avance-volumen">{{partida.item_subcontrato.importe_estimado_format}}</td>
                                    <td class="numerico avance-volumen">{{partida.item_subcontrato.cantidad_saldo_format}}</td>
                                    <td class="numerico avance-volumen">{{partida.item_subcontrato.importe_saldo_format}}</td>
                                </template>
                                <template v-else>
                                    <td >{{partida.tipo.descripcion}}</td>
                                    <td >{{partida.clave}}</td>
                                    <td ><span v-for="n in partida.nivel">&nbsp;</span>{{partida.descripcion}}</td>
                                    <td >{{partida.unidad}}</td>
                                    <td class="numerico">-</td>
                                    <td class="numerico">-</td>
                                    <td class="numerico">-</td>
                                    <td class="numerico">-</td>
                                    <td class="numerico">-</td>
                                    <td class="numerico">-</td>
                                </template>

                                <td class="numerico avance-importe" style="background-color: #ddd">{{partida.cantidad_format}}</td>
                                <td class="numerico saldo" style="background-color: #ddd">{{partida.precio_format}}</td>
                                <td class="numerico saldo" style="background-color: #ddd">{{partida.importe_format}}</td>
                                <td class="destino" v-if="partida.item_subcontrato" :title="partida.item_subcontrato.concepto_path">{{partida.item_subcontrato.concepto_path_corta}}</td>
                                <td class="destino" v-else :title="partida.concepto_path">{{partida.concepto_path_corta}}</td>
                            </tr>
                        </template>

                        </tbody>
                    </table>
                     <br />
                     <div class=" row" >
                        <label class="col-md-12 col-form-label">Observaciones:</label>
                     </div>
                     <div class=" row" >
                        <div class="col-md-12">
                           {{solicitud_cambio.observaciones}}
                        </div>
                     </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>

    import DatosSolicitud from "./DatosSolicitud";
    import DatosSubcontrato from "../../subcontrato/partials/DatosSubcontrato";
    import TablaDatosSubcontrato from "../../subcontrato/partials/TablaDatosSubcontrato";
    export default {
        name: "solicitud-cambio-partial-show",
        components: {TablaDatosSubcontrato, DatosSubcontrato, DatosSolicitud},
        props: ["id", "solicitud"],
        data() {
            return {
                cargando: true,
                columnas: [],
                solicitud_cambio: [],
            };
        },
        mounted() {
            this.find();
        },
        methods: {
            find() {
                if(this.id > 0){
                    return this.$store.dispatch('contratos/solicitud-cambio/find', {
                        id: this.id,
                        params: {
                            include: ['moneda', 'empresa', 'partidas.tipo', 'subcontrato', 'partidas.item_subcontrato.contrato', 'complemento.usuario']
                        }
                    }).then(data => {
                        this.solicitud_cambio = data;
                    }).finally(() => {
                        this.cargando = false;
                    })
                } else {
                    this.solicitud_cambio = this.solicitud;
                    this.cargando = false;
                }
            },
            regresar() {
                this.$router.push({name: 'solicitud-cambio'});
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

    table#tabla-conceptos th, table#tabla-conceptos td {
        border: 1px solid #dee2e6;
    }

    table thead th
    {
        padding: 0.2em;

        background-color: #f2f4f5;
        font-weight: bold;
        color: black;
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
