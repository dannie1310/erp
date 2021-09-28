<template>
    <section>
        <button @click="find" type="button" class="btn btn-sm btn-outline-primary" title="Ver Estados"><i class="fa fa-th-large"></i> </button>
        <div class="modal fade" ref="modal" tabindex="-1" role="dialog" aria-labelledby="modal">
             <div class="modal-dialog modal-lg" id="mdialTamanio">
                 <div class="modal-content">
                     <div class="modal-header">
                         <h4 class="modal-title"><i class="fa fa-th-large"></i> Consulta de estados de cotización</h4>
                         <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
                     </div>
                     <div class="modal-body modal-lg" v-if="solicitud">
                         <div class="row">
                             <div class="col-md-12">
                                 <encabezado-solicitud-compra v-bind:solicitud_compra="solicitud" />
                             </div>
                         </div>
                         <div class="row"  v-if="solicitud.estados_invitacion_cotizaciones">
                             <div  class="col-md-12">
                                 <div class="table-responsive">
                                     <table class="table table-bordered">
                                         <thead>
                                             <tr>
                                                 <th scope="col"></th>
                                                 <th class="c300 no_negrita" v-for="cotizacion in solicitud.estados_invitacion_cotizaciones.titulos">
                                                     <div class="row">
                                                         <div class="col-md-3">
                                                             <b>{{cotizacion.numero_folio}}</b>
                                                         </div>
                                                         <div class="col-md-6">
                                                             <b>{{cotizacion.empresa}}</b>
                                                         </div>
                                                         <div class="col-md-3" v-if="cotizacion.invitacion">
                                                             <b>{{cotizacion.invitacion == null ? '' : cotizacion.invitacion}}</b>
                                                         </div>
                                                     </div>
                                                 </th>
                                             </tr>
                                         </thead>
                                         <tbody v-for="partida in solicitud.estados_invitacion_cotizaciones.partidas">
                                             <tr>
                                                 <th style="text-align: left">{{partida.material}}</th>
                                                 <td v-for="p in partida.partidas" style="text-align: center">
                                                     <i class="fa fa-check" style="color: green" aria-hidden="true" v-if="p"></i>
                                                     <i class="fa fa-times" aria-hidden="true" style="color: red" v-else></i>
                                                 </td>
                                             </tr>
                                         </tbody>
                                     </table>
                                 </div>
                             </div>
                         </div>
                     </div>
                     <div class="modal-footer">
                         <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times-circle"></i>Cerrar</button>
                     </div>
                 </div>
             </div>
        </div>
    </section>
</template>

<script>
    import EncabezadoSolicitudCompra from "../solicitud-compra/partials/Encabezado";
    export default {
        name: "consulta-estado",
        components: {EncabezadoSolicitudCompra},
        props: ['id'],
        methods: {
            find() {
                this.cargando = true;
                this.$store.commit('compras/solicitud-compra/SET_SOLICITUD', null);
                return this.$store.dispatch('compras/solicitud-compra/find', {
                    id: this.id,
                    params:{include: ['partidas']}
                }).then(data => {
                    this.$store.commit('compras/solicitud-compra/SET_SOLICITUD', data);
                    $(this.$refs.modal).appendTo('body')
                    $(this.$refs.modal).modal('show');
                    this.cargando = false;
                })
            },
        },
        computed: {
            solicitud() {
                return this.$store.getters['compras/solicitud-compra/currentSolicitud']
            },
        }
    }
</script>

<style scoped>

</style>
