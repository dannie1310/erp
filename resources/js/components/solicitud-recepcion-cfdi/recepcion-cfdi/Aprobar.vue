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
            </div>
        </div>
        <div class="card" v-if="cargado && solicitud.cfdi.tipo_comprobante == 'I'">
            <div class="card-header">
                <h5>Datos del Contrarecibo</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group error-content">
                            <label for="rubro">Rubro de Factura:</label>
                            <model-list-select
                                name="id_rubro"
                                id="id_rubro"
                                placeholder="Seleccionar o buscar"
                                data-vv-as="Rubro"
                                v-validate="{required: true}"
                                v-model="id_rubro"
                                option-value="id"
                                option-text="descripcion"
                                :list="rubros"
                                :isError="errors.has('id_rubro')">
                            </model-list-select>
                            <div class="invalid-feedback" v-show="errors.has('id_rubro')">{{ errors.first('id_rubro') }}</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                            <div class="form-group error-content">
                                <label for="id_moneda">Moneda:</label>
                                <select
                                    class="form-control"
                                    name="id_moneda"
                                    id="id_moneda"
                                    data-vv-as="Moneda"
                                    v-validate="{required: true}"
                                    v-model="id_moneda"
                                >
                                    <option  v-for="(moneda, i) in monedas" :value="moneda.id" >
                                        {{ moneda.nombre }}
                                    </option>
                                </select>

                                <div class="invalid-feedback" v-show="errors.has('moneda')">{{ errors.first('moneda') }}</div>
                            </div>
                        </div>
                    <div class="col-md-2">
                        <div class="form-group error-content">
                            <label for="vencimiento">Vencimiento:</label>
                            <datepicker v-model = "vencimiento"
                                        name = "vencimiento"
                                        id = "vencimiento"
                                        :format = "formatoFecha"
                                        :language = "es"
                                        :bootstrap-styling = "true"
                                        class = "form-control"
                                        v-validate="{required: true}"
                                        :class="{'is-invalid': errors.has('vencimiento')}"
                            ></datepicker>
                            <div class="invalid-feedback" v-show="errors.has('vencimiento')">{{ errors.first('vencimiento') }}</div>
                        </div>
                    </div>
                    <div class="col-md-5">
                            <div class="form-group error-content">
                                <label for="observaciones">Observaciones:</label>
                                <textarea
                                    name="observaciones"
                                    id="observaciones"
                                    class="form-control"
                                    v-model="observaciones"
                                    v-validate="{required: true}"
                                    data-vv-as="Observaciones"
                                    :class="{'is-invalid': errors.has('observaciones')}"
                                ></textarea>
                                <div class="invalid-feedback" v-show="errors.has('observaciones')">{{ errors.first('observaciones') }}</div>
                            </div>
                        </div>
                </div>

            </div>
            <div class="card-footer">
                <span class="pull-right">
                    <button type="button" class="btn btn-secondary" v-on:click="regresar" >
                        <i class="fa fa-angle-left"></i>Regresar
                    </button>
                    <button v-if="solicitud.estado==0" @click="aprobar" title="Aprobar" class="btn btn-primary">
                        <i class="fa fa-check-circle"></i>Aprobar
                    </button>
                </span>
            </div>
        </div>
        <template v-else-if="cargado && solicitud.cfdi.tipo_comprobante == 'E' && solicitud.cfdi.cfdi_asociado">
            <template v-if="solicitud.cfdi.cfdi_asociado.transaccion_factura && (solicitud.cfdi.tipo_relacion == 1 || solicitud.cfdi.tipo_relacion == 7)">
                <div class="card" v-if="solicitud.cfdi.cfdi_asociado.transaccion_factura.estado==0">
                    <div class="card-header">
                        <h5>Datos del Contrarecibo del CFDI Asociado</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning" role="alert">
                           <i class="fa fa-info-circle"></i> La nota de crédito se incorporará al contrarecibo restandose al monto de la factura
                        </div>
                        <div class="row" >
                            <div class="col-md-2">
                                <label >Folio CR:</label>
                                <div>
                                    {{solicitud.cfdi.cfdi_asociado.transaccion_factura.contra_recibo.numero_folio_format}}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label >Folio Factura:</label>
                                <div>
                                    {{solicitud.cfdi.cfdi_asociado.transaccion_factura.numero_folio_format}}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label >Fecha:</label>
                                <div>
                                    {{solicitud.cfdi.cfdi_asociado.transaccion_factura.fecha_format}}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label >Monto:</label>
                                <div>
                                    {{solicitud.cfdi.cfdi_asociado.transaccion_factura.monto_format}}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label >Observaciones:</label>
                                <div>
                                    {{solicitud.cfdi.cfdi_asociado.transaccion_factura.observaciones}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <span class="pull-right">
                            <button type="button" class="btn btn-secondary" v-on:click="regresar" >
                                <i class="fa fa-angle-left"></i>Regresar
                            </button>
                            <button v-if="solicitud.estado==0" @click="aprobar" title="Aprobar" class="btn btn-primary">
                                <i class="fa fa-check-circle"></i>Aprobar
                            </button>
                        </span>
                    </div>
                </div>
            </template>
        </template>
    </span>
</template>

<script>
    import datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    import {ModelListSelect} from "vue-search-select";

    export default {
        name: "solicitud-recepcion-cfdi-aprobar",
        components: {datepicker, ModelListSelect},
        props: ["id"],
        data() {
            return {
                es:es,
                cargando:true,
                cargado:false,
                vencimiento:new Date(),
                id_rubro:'',
                id_factura:'',
                id_moneda: 1,
                observaciones:'',
                rubros:[],
                monedas:[]
            }
        },
        mounted() {
            this.find();
        },
        computed: {
            solicitud(){
                return this.$store.getters['recepcion-cfdi/solicitud-recepcion-cfdi/currentSolicitud'];
            },
        },
        methods:{
            find() {
                this.cargando = true;
                this.cargado = false;
                this.$store.commit('recepcion-cfdi/solicitud-recepcion-cfdi/SET_SOLICITUD', null);
                return this.$store.dispatch('recepcion-cfdi/solicitud-recepcion-cfdi/find', {
                    id: this.id,
                    params: {include: ['cfdi.conceptos', 'empresa', 'obra', 'cfdi.cfdi_asociado.transaccion_factura.contra_recibo']}
                }).then(data => {
                    this.$store.commit('recepcion-cfdi/solicitud-recepcion-cfdi/SET_SOLICITUD', data);
                    if(data.cfdi.tipo_comprobante == 'E' && data.cfdi.cfdi_asociado && (data.cfdi.tipo_relacion == 1 || data.cfdi.tipo_relacion == 7)){
                        if(data.cfdi.cfdi_asociado.transaccion_factura){
                            if(data.cfdi.cfdi_asociado.transaccion_factura.estado==0){
                                this.id_factura = data.cfdi.cfdi_asociado.transaccion_factura.id;
                            }
                        }
                    }
                }).finally(() => {
                    this.getRubros();
                })
            },
            aprobar() {
                this.$validator.validate().then(result => {
                    if (result) {

                        return this.$store.dispatch('recepcion-cfdi/solicitud-recepcion-cfdi/aprobar', {
                            id: this.id,
                            id_rubro: this.id_rubro,
                            id_factura: this.id_factura,
                            id_moneda: this.id_moneda,
                            observaciones: this.observaciones,
                            vencimiento: this.vencimiento
                        }).then(data => {
                            this.$router.push({name: 'recepcion-cfdi'});
                        })
                    }
                });
            },
            regresar() {
                this.$router.push({name: 'recepcion-cfdi'});
            },
            getMonedas() {
                this.cargando =true;
                return this.$store.dispatch('cadeco/moneda/index', {
                    params: {sort: 'nombre', order: 'desc' }
                })
                    .then(data => {
                        this.monedas = data.data;
                    })
                    .finally(()=>{
                        this.cargando = false;
                        this.cargado = true;
                    })
            },
            getRubros() {
                this.cargado = true;
                return this.$store.dispatch('finanzas/rubro/index', {
                    params: {sort: 'descripcion', order: 'asc', scope:'paraFactura' }
                })
                .then(data => {
                    this.rubros = data.data;
                })
                .finally(()=>{
                    this.getMonedas();
                })
            },
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
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
