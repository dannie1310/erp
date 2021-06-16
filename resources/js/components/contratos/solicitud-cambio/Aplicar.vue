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
        <span v-else>
            <div class="card">
                <div class="card-body">
                    <div class="card-body">
                        <solicitud-cambio-partial-show v-bind:solicitud="solicitud_cambio"></solicitud-cambio-partial-show>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="pull-right">
                        <button type="button" class="btn btn-secondary" v-on:click="regresar"><i class="fa fa-angle-left"></i>Regresar</button>
                        <button type="button" class="btn btn-danger" v-on:click="aplicar"><i class="fa fa-thumbs-o-up"></i>Aplicar</button>
                        <button type="button" class="btn btn-warning" v-on:click="pideMotivoRechazo"><i class="fa fa-thumbs-o-down"></i>Rechazar</button>
                    </div>
                </div>
            </div>
        </span>

        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-unidad"> <i class="fa fa-thumbs-o-down"></i> Rechazar Solicitud de Cambio a Subcontrato</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" >
                        <div class="row">
                            <div class="col-md-12">
                                <label for="motivo">Motivo de rechazo:</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row error-content">

                                    <div class="col-md-12">
                                        <textarea
                                            name="motivo"
                                            id="motivo"
                                            class="form-control"
                                            v-model="motivo"
                                            v-validate="{required: true}"
                                            data-vv-as="Motivo"
                                            :class="{'is-invalid': errors.has('motivo')}"
                                        ></textarea>
                                        <div class="invalid-feedback" v-show="errors.has('motivo')">{{ errors.first('motivo') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fa fa-times-circle"></i>
                            Cerrar
                        </button>
                        <button type="button" class="btn btn-warning" @click="rechazar" :disabled="errors.count() > 0 || motivo == ''">
                            <i class="fa fa-thumbs-o-down"></i>
                            Rechazar
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </span>
</template>

<script>

    import SolicitudCambioShow from "./Show";
    import DatosSolicitud from "./partials/DatosSolicitud";
    import TablaDatosSolicitudCambioSubcontrato from "./partials/TablaDatosSolicitudCambioSubcontrato";
    import TablaDatosSubcontrato from "../subcontrato/partials/TablaDatosSubcontrato";
    import SolicitudCambioPartialShow from "./partials/PartialShow";
    export default {
        name: "solicitud-cambio-aplicar",
        components: {
            SolicitudCambioPartialShow,
            TablaDatosSubcontrato, TablaDatosSolicitudCambioSubcontrato, DatosSolicitud, SolicitudCambioShow},
        props: ["id"],
        data() {
            return {
                cargando: true,
                columnas: [],
                solicitud_cambio: [],
                motivo:''
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
                        include: ['moneda', 'empresa', 'partidas.tipo', 'subcontrato', 'partidas.item_subcontrato.contrato', 'complemento.usuario']
                    }
                }).then(data => {
                    this.solicitud_cambio = data;
                }).finally(() => {
                    this.cargando = false;
                })
            },
            regresar() {
                this.$router.push({name: 'solicitud-cambio'});
            },
            aplicar() {
                return this.$store.dispatch('contratos/solicitud-cambio/aplicar', {
                    id: this.id
                }).then(data => {
                    this.$router.push({name: 'solicitud-cambio'});
                })
            },
            pideMotivoRechazo() {
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            },
            rechazar() {
                this.$validator.validate().then(result => {
                    if (result) {
                        if(this.motivo == '') {
                            swal('¡Error!', 'Debe colocar un motivo para realizar el rechazo.', 'error')
                        }
                        else {
                            return this.$store.dispatch('contratos/solicitud-cambio/rechazar', {
                                id: this.id,
                                params:{motivo:this.motivo}
                            }).then(data => {
                                $(this.$refs.modal).modal('hide');
                            }).finally( ()=>{
                                this.$router.push({name: 'solicitud-cambio'});
                            });
                        }
                    }
                });
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
