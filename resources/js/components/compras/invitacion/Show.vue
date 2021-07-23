<template>
    <span>

        <div class="card" v-if="!invitacion">
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

        <div class="card" v-else>
            <div class="card-body">
                <div class="row" >
                    <div class="col-md-12">
                        <invitacion-compra-encabezado v-bind:invitacion="invitacion"></invitacion-compra-encabezado>
                        <invitacion-compra-tabla-completa-datos v-bind:invitacion="invitacion"></invitacion-compra-tabla-completa-datos>
                    </div>
                </div>
                <div class="row" >
                    <div class="col-md-12">
                        <Documento v-bind:url="url" v-bind:id="invitacion.carta_terminos.id" v-bind:descripcion="invitacion.carta_terminos.tipo_archivo.descripcion" v-bind:texto="invitacion.carta_terminos.tipo_archivo.descripcion" ></Documento>
                        <DescargaDocumento v-if="invitacion.formato_cotizacion" v-bind:url="url_descarga" v-bind:id="invitacion.formato_cotizacion.id" v-bind:descripcion="invitacion.formato_cotizacion.tipo_archivo.descripcion" v-bind:texto="invitacion.formato_cotizacion.tipo_archivo.descripcion"></DescargaDocumento>
                    </div>
                </div>
            </div>
        </div>
    </span>

</template>

<script>
    import InvitacionCompraEncabezado from "./partials/Encabezado";
    import InvitacionCompraTablaCompletaDatos from "./partials/TablaCompletaDatosInvitacion";
    import Documento from "../../globals/archivos/Documento";
    import DescargaDocumento from "../../globals/archivos/DescargaDocumento";
    export default {
        name: "InvitacionCompraShow",
        components: {DescargaDocumento, Documento, InvitacionCompraTablaCompletaDatos, InvitacionCompraEncabezado},
        props: ['id'],
        data(){
            return{
                cargando: false,
                url : '/api/padron-proveedores/archivo-invitacion/{id}/documento?access_token='+this.$session.get('jwt'),
                url_descarga : '/api/padron-proveedores/archivo-invitacion/{id}/documento/descargar?access_token='+this.$session.get('jwt'),
            }
        },
        mounted() {
            this.find();
        },
        methods: {
            find() {
                this.cargando = true;
                this.$store.commit('compras/invitacion/SET_INVITACION', null);
                return this.$store.dispatch('compras/invitacion/find', {
                    id: this.id,
                    params:{include: [ "carta_terminos", "formato_cotizacion"
                            ]}
                }).then(data => {
                    this.$store.commit('compras/invitacion/SET_INVITACION', data);
                })
                .finally(()=> {
                    this.cargando = false;
                })
            },
        },
        computed: {
            invitacion() {
                return this.$store.getters['compras/invitacion/currentInvitacion']
            },
        }
    }
</script>

<style scoped>

</style>
