<template>
    <span>
        <div class="card" v-if="!cfdi">
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

        <div class="card" v-else>
            <div class="card-header">
                <h5>Datos del CFDI</h5>
            </div>
            <div class="card-body">
                <span>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                            <label >Emisión:</label>
                                <input class="form-control" v-model="cfdi.fecha_format" readonly="readonly" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                            <label >Serie y Folio:</label>
                                <input class="form-control" v-model="cfdi.referencia" readonly="readonly" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                            <label >Tipo:</label>
                                <input class="form-control" v-model="cfdi.tipo_comprobante" readonly="readonly" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                            <label >UUID:</label>
                                <input class="form-control" v-model="cfdi.uuid" readonly="readonly" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                            <label >Emisor:</label>
                                <input class="form-control" v-model="cfdi.proveedor.razon_social" readonly="readonly" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >RFC Emisor:</label>
                                <input class="form-control" v-model="cfdi.proveedor.rfc" readonly="readonly" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                            <label >Receptor:</label>
                                <input class="form-control" v-model="cfdi.empresa.razon_social" readonly="readonly" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >RFC Receptor:</label>
                                <input class="form-control" v-model="cfdi.empresa.rfc" readonly="readonly" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Descuento:</label>
                                <input class="form-control" v-model="cfdi.descuento_format" readonly="readonly" style="text-align: right" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Impuestos Retenidos:</label>
                                <input class="form-control" v-model="cfdi.impuestos_retenidos_format" readonly="readonly" style="text-align: right" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Impuestos Trasladados:</label>
                                <input class="form-control" v-model="cfdi.impuestos_trasladados_format" readonly="readonly" style="text-align: right" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Total:</label>
                                <input class="form-control" v-model="cfdi.total_format" readonly="readonly" style="text-align: right" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Moneda:</label>
                                <input class="form-control" v-model="cfdi.moneda" readonly="readonly" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Tipo de Cambio:</label>
                                <input class="form-control" v-model="cfdi.tipo_cambio" readonly="readonly" style="text-align: right" />
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
                            <template v-for="(concepto, i) in cfdi.conceptos.data">
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
                                <tr v-for="(traslado, i) in concepto.traslados.data">
                                    <td colspan="9">
                                        <b>Impuesto Trasladado</b> Base: {{traslado.base_format}} Impuesto: {{traslado.impuesto_txt}} Tipo Factor: {{traslado.tipo_factor}} Tasa o Cuota: {{traslado.tasa_o_cuota}} Importe: {{traslado.importe_format}}
                                    </td>
                                </tr>
                            </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </span>


</template>

<script>

export default {
    name: "cfdi-show",
    props: ["cfdi"],
    data() {
        return {
            cargando:true,
            cargado:false,
        }
    },
    mounted() {

    },
    computed: {

    },
    methods:{
        find() {

        },
        regresar() {
            this.$router.go(-1);
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
