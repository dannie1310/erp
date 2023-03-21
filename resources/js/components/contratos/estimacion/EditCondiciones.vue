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
        <div v-else>
            <div class="d-flex flex-row-reverse">
                <div class="p-2" v-if="estimacion.estado == 0">
                    <Penalizacion v-bind:id="id"></Penalizacion>
                </div>
                <div class="p-2">
                    <Resumen v-bind:id="id" v-bind:cargando="cargando"></Resumen>
                </div>
                <div class="p-2" v-if="estimacion.estado == 0">
                    <Amortizacion v-bind:id="id"></Amortizacion>
                </div>
                <div class="p-2" v-if="estimacion.estado == 0">
                    <RetencionIndex v-bind:id="id"></RetencionIndex>
                </div>
                <div class="p-2" v-if="estimacion.estado == 0">
                    <RetencionISRCreate v-bind:id="id"></RetencionISRCreate>
                </div>
                <div class="p-2" v-if="estimacion.estado == 0">
                    <RetencionIvaCreate v-bind:id="id"></RetencionIvaCreate>
                </div>
                <div class="p-2" v-if="estimacion.estado == 0">
                    <DeductivaEdit v-bind:id="id" v-bind:id_empresa="estimacion?estimacion.id_empresa:''"></DeductivaEdit>
                </div>
            </div>
            <div class="row">
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
                                        {{estimacion.numero_folio_format}}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">Folio Consecutivo</label>
                                    <div class="col-md-9">
                                        {{estimacion.consecutivo}}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">Objeto</label>
                                    <div class="col-md-9">
                                        ({{estimacion.subcontrato.numero_folio_format}}) {{ estimacion.subcontrato.referencia }}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">Contratista</label>
                                    <div class="col-md-9">
                                        {{ estimacion.empresa.razon_social }}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">Observaciones</label>
                                    <div class="col-md-9">{{estimacion.observaciones}}</div>
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
                                            {{estimacion.fecha_inicial}}
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="col-form-label">Término</label>
                                            {{estimacion.fecha_final}}
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <button type="button" class="btn btn-secondary pull-right" v-on:click="salir"><i class="fa fa-times"></i>Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
     </span>
</template>

<script>
    import RetencionISRCreate from './retencion-isr/Create';
    import RetencionIvaCreate from './retencion-iva/create'
    import DeductivaEdit from './deductivas/Edit'
    import RetencionIndex from './retenciones/Index';
    import Amortizacion from './amortizacion/Edit';
    import Penalizacion from './penalizacion/Index';
    import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    import Resumen from './resumen/Show'

    export default {
        name: "estimacion-edit-condiciones",
        components: {DeductivaEdit, RetencionIndex, RetencionIvaCreate, Amortizacion, Datepicker, es, Resumen, Penalizacion,RetencionISRCreate},
        props: ['id'],
        data() {
            return {
                cargando: true,
                es:es,
                columnas: [],
                estimacion : []
            }
        },
        mounted() {
            this.cargando = true;
            this.find()
        },
        methods: {
            find() {
                return this.$store.dispatch('contratos/estimacion/find', {
                    id: this.id,
                    params: {include:['subcontrato', 'empresa']}
                }).then(data => {
                    this.estimacion = data;
                }).finally(() => {
                    this.cargando = false;
                })
            },
            salir(){
                this.$router.push({name: 'estimacion'});
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
