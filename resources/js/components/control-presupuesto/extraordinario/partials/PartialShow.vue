<template>
    <span>
        <div class="spinner-border text-success" role="status" v-if="cargando">
           <span class="sr-only">Cargando...</span>
        </div>

        <div  v-if="!cargando">
            <div class="row">
                <div class="col-md-12">
                    <DatosSolicitud v-bind:solicitud_cambio="solicitud_cambio" v-if="!cargando"></DatosSolicitud>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 table-responsive">
                    <span>Partidas de Solicitud de Cambio</span>
                     <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th class="text-center">Descripción</th>
                                <th class="text-center">Unidad</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-center">Precio Unitario</th>
                                <th class="text-center">Importe</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(partida, i) in solicitud_cambio.partidas.data">
                                <td>{{i + 1}}</td>
                                <td :title="partida.descripcion"><span v-for="n in partida.longitud_nivel*2">-</span>{{partida.descripcion}}</td>
                                <td>{{partida.unidad}}</td>
                                <td style="text-align: right">{{partida.cantidad_format}}</td>
                                <td style="text-align: right">{{partida.precio_unitario_format}}</td>
                                <td style="text-align: right">{{partida.importe_format}}</td>
                             </tr>
                         </tbody>
                     </table>
                </div>
            </div>
        </div>
    </span>
</template>

<script>

    import DatosSolicitud from "./DatosSolicitud";
    import TablaDatosSolicitudCambioPresupuesto from "./TablaDatosSolicitudCambioPresupuesto";
    export default {
        name: "solicitud-cambio-presupuesto-partial-show",
        components: {TablaDatosSolicitudCambioPresupuesto, DatosSolicitud},
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
