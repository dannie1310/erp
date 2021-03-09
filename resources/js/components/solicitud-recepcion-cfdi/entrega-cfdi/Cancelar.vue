<template>
    <span>
        <div class="card" v-if="cargando">
            <div class="card-body">
                <div >
                    <div class="row" >
                        <div class="col-md-12">
                            <div class="spinner-border text-success" role="status">
                               <span class="sr-only">Cargando...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card" v-if="cargado">
            <div class="card-header">
                <h5>Datos de la Solicitud de Recepción</h5>
            </div>
            <div class="card-body">
                <span>
                    <div class="row">
                        <div class="col-md-2 offset-md-8">
                            <div class="form-group">
                                <label >No. de Folio:</label>
                                <input class="form-control"
                                       name = "numero_folio"
                                       v-model="solicitud.numero_folio"
                                       readonly="readonly"
                                />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Fecha:</label>
                                <input class="form-control"
                                       v-model="solicitud.fecha_registro"
                                       name = "fecha_format"
                                       readonly="readonly"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Proyecto:</label>
                                <input class="form-control"
                                       name = "contacto"
                                       v-model="solicitud.obra.nombre"
                                       readonly="readonly"
                                />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Contacto HI:</label>
                                <input class="form-control"
                                       name = "contacto"
                                       v-model="solicitud.contacto"
                                       readonly="readonly"
                                />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Correo para recibir notificaciones:</label>
                                <input class="form-control"
                                       v-model="solicitud.correo_notificaciones"
                                       name = "correo"
                                       readonly="readonly"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group ">
                                <label >Observaciones:</label>
                                <textarea class="form-control"
                                          v-model="solicitud.observaciones"
                                          name = "observaciones"
                                          id = "observaciones"
                                          readonly="readonly"
                                ></textarea>
                            </div>
                        </div>
                    </div>
                </span>
            </div>
        </div>
        <div class="card" v-if="cargado">
            <div class="card-header">
                <h5>Datos del CFDI</h5>
            </div>
            <div class="card-body">
                <span>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                            <label >Emisión:</label>
                                <input class="form-control" v-model="solicitud.cfdi.fecha_format" readonly="readonly" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                            <label >Serie y Folio:</label>
                                <input class="form-control" v-model="solicitud.cfdi.referencia" readonly="readonly" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                            <label >Tipo:</label>
                                <input class="form-control" v-model="solicitud.cfdi.tipo_comprobante" readonly="readonly" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                            <label >UUID:</label>
                                <input class="form-control" v-model="solicitud.cfdi.uuid" readonly="readonly" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group">
                            <label >Empresa:</label>
                                <input class="form-control" v-model="solicitud.cfdi.empresa.razon_social" readonly="readonly" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >RFC:</label>
                                <input class="form-control" v-model="solicitud.cfdi.empresa.rfc" readonly="readonly" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Descuento:</label>
                                <input class="form-control" v-model="solicitud.cfdi.descuento_format" readonly="readonly" style="text-align: right" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Impuestos Retenidos:</label>
                                <input class="form-control" v-model="solicitud.cfdi.impuestos_retenidos_format" readonly="readonly" style="text-align: right" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Impuestos Trasladados:</label>
                                <input class="form-control" v-model="solicitud.cfdi.impuestos_trasladados_format" readonly="readonly" style="text-align: right" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Total:</label>
                                <input class="form-control" v-model="solicitud.cfdi.total_format" readonly="readonly" style="text-align: right" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Moneda:</label>
                                <input class="form-control" v-model="solicitud.cfdi.moneda" readonly="readonly" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Tipo de Cambio:</label>
                                <input class="form-control" v-model="solicitud.cfdi.tipo_cambio" readonly="readonly" style="text-align: right" />
                            </div>
                        </div>
                    </div>
                </span>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="index_corto">#</th>
                                    <th class="no_parte">Clave Producto / Servicio</th>
                                    <th>Descripción</th>
                                    <th>Clave Unidad</th>
                                    <th>Unidad</th>
                                    <th>Cantidad</th>
                                    <th>Valor Unitario</th>
                                    <th>Descuento</th>
                                    <th>Importe</th>
                                </tr>
                            </thead>
                            <tbody>
                            <template v-for="(concepto, i) in solicitud.cfdi.conceptos.data">
                                <tr >
                                    <td>{{i+1}}</td>
                                    <td>{{concepto.clave_prod_serv}}</td>
                                    <td>{{concepto.descripcion}}</td>
                                    <td>{{concepto.clave_unidad}}</td>
                                    <td>{{concepto.unidad}}</td>
                                    <td style="text-align: right">{{concepto.cantidad_format}}</td>
                                    <td style="text-align: right">{{concepto.valor_unitario_format}}</td>
                                    <td style="text-align: right">{{concepto.descuento_format}}</td>
                                    <td style="text-align: right">{{concepto.importe_format}}</td>
                                </tr>
                            </template>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label>Motivo de Cancelación: </label>
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
                                    data-vv-as="Motivo de Cancelación"
                                    :class="{'is-invalid': errors.has('motivo')}"
                                ></textarea>
                                <div class="invalid-feedback" v-show="errors.has('motivo')">{{ errors.first('motivo') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <span class="pull-right">
                    <button type="button" class="btn btn-secondary" v-on:click="regresar" >
                        <i class="fa fa-angle-left"></i>Regresar
                    </button>
                    <button v-if="solicitud.estado==0" @click="cancelar" title="Cancelar" class="btn btn-danger">
                        <i class="fa fa-times-circle"></i>Cancelar
                    </button>
                </span>
            </div>
        </div>
    </span>
</template>

<script>

    export default {
        name: "solicitud-recepcion-cfdi-cancelar",
        props: ["id"],
        data() {
            return {
                cargando:true,
                cargado:false,
                motivo:''
            }
        },
        mounted() {
            this.find();

        },
        computed: {
            solicitud(){
                return this.$store.getters['entrega-cfdi/solicitud-recepcion-cfdi/currentSolicitud'];
            },
        },
        methods:{
            find() {
                this.cargando = true;
                this.cargado = false;
                this.$store.commit('entrega-cfdi/solicitud-recepcion-cfdi/SET_SOLICITUD', null);
                return this.$store.dispatch('entrega-cfdi/solicitud-recepcion-cfdi/find', {
                    id: this.id,
                    params: {include: ['cfdi.conceptos', 'empresa', 'obra']}
                }).then(data => {
                    this.$store.commit('entrega-cfdi/solicitud-recepcion-cfdi/SET_SOLICITUD', data);
                }).finally(() => {
                    this.cargando = false;
                    this.cargado = true;
                })
            },
            cancelar() {
                this.$validator.validate().then(result => {
                    if (result) {
                        return this.$store.dispatch('entrega-cfdi/solicitud-recepcion-cfdi/cancelar', {
                            id: this.id,
                            motivo: this.motivo
                        }).then(data => {
                            this.$router.push({name: 'entrega-cfdi'});
                        })
                    }
                });
            },
            regresar() {
                this.$router.push({name: 'entrega-cfdi'});
            },
        }
    }
</script>
<style>
    .dropzone-custom-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
    }

    .dropzone-custom-title {
        margin-top: 0;
        color: #999;
    }

    .subtitle {
        color: #7ac142;
    }
    .vue-dropzone {
        border: 2px dashed #e5e5e5;
    }
</style>
