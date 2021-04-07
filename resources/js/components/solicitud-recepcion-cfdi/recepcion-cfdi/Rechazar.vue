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
        <div class="card" v-if="cargado" >
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <label>Motivo de Rechazo: </label>
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
                                    data-vv-as="Motivo de Rechazo"
                                    :class="{'is-invalid': errors.has('motivo')}"
                                ></textarea>
                                <div class="invalid-feedback" v-show="errors.has('motivo')">{{ errors.first('motivo') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <solicitud-recepcion-cfdi-detalle v-bind:solicitud="solicitud" v-if="solicitud"></solicitud-recepcion-cfdi-detalle>
        <div class="pull-right" style="padding-bottom: 48px">
            <button type="button" class="btn btn-secondary" v-on:click="regresar" >
                <i class="fa fa-angle-left"></i>Regresar
            </button>
            <button v-if="solicitud.estado==0" @click="rechazar" title="Rechazar" class="btn btn-danger">
                <i class="fa fa-times-circle"></i>Rechazar
            </button>
        </div>

    </span>
</template>

<script>

    import SolicitudRecepcionCfdiDetalle from "../partials/Detalle";
    export default {
        name: "solicitud-recepcion-cfdi-rechazar",
        components: {SolicitudRecepcionCfdiDetalle},
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
                    params: {include: ['cfdi.conceptos', 'empresa', 'obra']}
                }).then(data => {
                    this.$store.commit('recepcion-cfdi/solicitud-recepcion-cfdi/SET_SOLICITUD', data);
                }).finally(() => {
                    this.cargando = false;
                    this.cargado = true;
                })
            },
            rechazar() {
                this.$validator.validate().then(result => {
                    if (result) {
                        return this.$store.dispatch('recepcion-cfdi/solicitud-recepcion-cfdi/rechazar', {
                            id: this.id,
                            motivo: this.motivo
                        }).then(data => {
                            this.$router.push({name: 'recepcion-cfdi'});
                        })
                    }
                });
            },
            regresar() {
                this.$router.push({name: 'recepcion-cfdi'});
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
