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
                        <datos-invitacion v-bind:invitacion="this.invitacion"></datos-invitacion>
                    </div>
                </div>
                <div class="row" >
                    <div class="col-md-6">
                        <Documento v-if="invitacion.carta_terminos" v-bind:url="url" v-bind:id="invitacion.carta_terminos.id" v-bind:descripcion="invitacion.carta_terminos.tipo_archivo.descripcion" v-bind:texto="invitacion.carta_terminos.tipo_archivo.descripcion" ></Documento>
                        <DescargaDocumento v-if="invitacion.formato_cotizacion" v-bind:url="url_descarga" v-bind:id="invitacion.formato_cotizacion.id" v-bind:descripcion="invitacion.formato_cotizacion.tipo_archivo.descripcion" v-bind:texto="invitacion.formato_cotizacion.tipo_archivo.descripcion"></DescargaDocumento>
                        <formato-invitacion-cotizacion-compra v-bind:id="invitacion.id" v-bind:db="invitacion.base_datos" v-bind:id_obra="invitacion.id_obra" v-bind:texto="'Invitación a Cotizar'"></formato-invitacion-cotizacion-compra>
                    </div>
                    <div class="col-md-6">
                        <div class="pull-right">
                            <button type="button" class="btn btn-secondary" v-on:click="salir"><i class="fa fa-angle-left"></i>Regresar</button>
                            <button type="button" class="btn btn-primary" v-on:click="cotizar" :disabled="!invitacion"><i class="fa fa-comment-dollar"></i>Cotizar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>

</template>

<script>

    import Documento from "../../globals/archivos/Documento";
    import DescargaDocumento from "../../globals/archivos/DescargaDocumento";
    import FormatoSolicitudCompra from "../../compras/solicitud-compra/FormatoSolicitudCompra";
    import InvitacionCompraEncabezado from "../../compras/invitacion/partials/Encabezado";
    import InvitacionCompraTablaCompletaDatos from "../../compras/invitacion/partials/TablaCompletaDatosInvitacion";
    import DatosInvitacion from "../invitacion/partials/DatosInvitacion";
    import FormatoInvitacionCotizacionCompra
        from "../../compras/solicitud-compra/partials/FormatoInvitacionCotizacionCompra";

    export default {
        name: "PrevisualizarInvitacion",
        components: {
            FormatoInvitacionCotizacionCompra,
            DatosInvitacion,
            InvitacionCompraTablaCompletaDatos,
            InvitacionCompraEncabezado,
            FormatoSolicitudCompra,
            DescargaDocumento, Documento},
        props: ['id_invitacion'],
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
                this.$store.commit('padronProveedores/invitacion/SET_INVITACION', null);
                return this.$store.dispatch('padronProveedores/invitacion/find', {
                    id: this.id_invitacion,
                    params:{include: [ "carta_terminos", "formato_cotizacion"
                            ]}
                }).then(data => {
                    this.$store.commit('padronProveedores/invitacion/SET_INVITACION', data);
                })
                .finally(()=> {
                    this.cargando = false;
                })
            },
            salir() {
                this.$router.go(-1);
            },
            cotizar() {
                return this.$store.dispatch('padronProveedores/invitacion/abrir', {
                    id: this.id_invitacion,
                    params:{}
                }).then(data => {
                    this.$router.push({name: 'cotizacion-proveedor-create', params: {id_invitacion: this.id_invitacion}});
                });
            },
        },
        computed: {
            invitacion() {
                return this.$store.getters['padronProveedores/invitacion/currentInvitacion']
            },
        }
    }
</script>

<style scoped>

</style>
