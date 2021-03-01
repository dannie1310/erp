<template>
    <span>
        <div class="card" v-if="cargado">
            <div class="card-header">
                <h5>Datos de CFDI</h5>
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
                        <div class="col-md-10">
                            <div class="form-group">
                            <label >Empresa:</label>
                                <input class="form-control" v-model="cfdi.empresa.razon_social" readonly="readonly" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >RFC:</label>
                                <input class="form-control" v-model="cfdi.empresa.rfc" readonly="readonly" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Impuestos Retenidos:</label>
                                <input class="form-control" v-model="cfdi.impuestos_retenidos_format" readonly="readonly" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Impuestos Trasladados:</label>
                                <input class="form-control" v-model="cfdi.impuestos_trasladados_format" readonly="readonly" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Total:</label>
                                <input class="form-control" v-model="cfdi.total_format" readonly="readonly" />
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
                                <input class="form-control" v-model="cfdi.tipo_cambio" readonly="readonly" />
                            </div>
                        </div>
                    </div>
                    <hr>
                </span>
            </div>
            <div class="card-footer">
                <span class="pull-right">
                    <button type="button" class="btn btn-secondary" >
                        <i class="fa fa-angle-left"></i>Regresar
                    </button>
                    <button type="button" class="btn btn-primary" >
                        Continuar <i class="fa fa-angle-right"></i>
                    </button>
                </span>
            </div>
        </div>
    </span>


</template>

<script>

    export default {
        name: "solicitud-recepcion-cfdi-create",
        props: ["id_cfdi"],
        data() {
            return {
                cargando:true,
                cargado:false,
            }
        },

        mounted() {
            this.find();

        },
        computed: {
            cfdi(){
                return this.$store.getters['fiscal/cfd-sat/currentCFDSAT'];
            },
        },
        methods:{
            find(){
                this.cargado = false;
                if(this.$store.getters['fiscal/cfd-sat/currentCFDSAT'] == null){
                    this.$store.commit('fiscal/cfd-sat/SET_cCFDSAT', null);
                    return this.$store.dispatch('fiscal/cfd-sat/find', {
                        id: this.id_cfdi,
                        params:{}
                    }).then(data => {
                        this.$store.commit('fiscal/cfd-sat/SET_cCFDSAT', data);
                    }).finally(()=>{
                        this.cargado = true;
                    });
                } else {
                    this.cargado = true;
                }
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store()
                    }
                });
            },
            store() {
                return this.$store.dispatch('finanzas/factura/store', this.$data.dato)
                .then(data => {
                    this.$emit('created', data);
                    $(this.$refs.modal).modal('hide');
                    this.cleanData();
                }).finally( ()=>{
                    this.cargando = false;
                });
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
