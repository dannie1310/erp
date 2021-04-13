<template>
    <span>
        <div class="card" v-if="!solicitud">
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
        <div v-else>
            <encabezado v-bind:solicitud="solicitud"></encabezado>
            <solicitud-recepcion-cfdi-detalle v-bind:solicitud="solicitud" v-if="solicitud" v-bind:configuracion="{}"></solicitud-recepcion-cfdi-detalle>
            <div class="pull-right" style="padding-bottom: 48px" v-if="solicitud">
                <button type="button" class="btn btn-secondary"  v-on:click="regresar" >
                    <i class="fa fa-angle-left"></i>Regresar
                </button>
            </div>
        </div>
    </span>
</template>

<script>

    import SolicitudRecepcionCfdiDetalle from "../partials/Detalle";
    import Encabezado from "../partials/Encabezado";
    export default {
        name: "solicitud-recepcion-cfdi-show",
        components: {Encabezado, SolicitudRecepcionCfdiDetalle},
        props: ["id"],
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
