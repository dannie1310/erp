<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-primary" title="Ver Estados"><i class="fa fa-th-large"></i> </button>
        <div class="modal fade" ref="modal" tabindex="-1" role="dialog" aria-labelledby="modal">
             <div class="modal-dialog modal-lg" id="mdialTamanio">
                 <div class="modal-content">
                     <div class="modal-header">
                         <h4 class="modal-title"><i class="fa fa-th-large"></i> Consulta de estados de cotización</h4>
                         <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
                     </div>
                     <div class="modal-body modal-lg">
                         <div v-if="cargando">
                             <div class="card">
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
                         </div>
                         <div v-else>
                             <div class="row" v-if="solicitud">
                                 <div class="col-md-12">
                                     <encabezado-solicitud-compra v-bind:solicitud_compra="solicitud" />
                                 </div>
                             </div>
                             <div class="row"  v-if="solicitud">
                                 <div  class="col-md-12">
                                     <div class="table-responsive">
                                         <table class="table table-bordered">
                                             <thead>
                                                 <tr>
                                                     <td style="border-style: none;" colspan="2"></td>
                                                     <th v-for="cotizacion in solicitud.detalleEstadoCotizacion.titulos" style="padding: 2px;" v-if="cotizacion.invitacion">
                                                         <label ><b>Invitación: {{cotizacion.invitacion}}</b></label>
                                                     </th>
                                                 </tr>
                                                 <tr>
                                                     <td style="border-style: none;" colspan="2"></td>
                                                     <th v-for="cotizacion in solicitud.detalleEstadoCotizacion.titulos" style="padding: 2px;"><label><b>Cotización: <b>{{cotizacion.numero_folio}}</b></b></label></th>
                                                 </tr>
                                                 <tr>
                                                     <td style="border-style: none;" colspan="2"></td>
                                                     <th v-for="cotizacion in solicitud.detalleEstadoCotizacion.titulos" style="padding: 1px;" ><b>{{cotizacion.empresa}}</b></th>
                                                 </tr>
                                             </thead>
                                             <tbody>
                                                 <tr v-for="(partida, i) in solicitud.detalleEstadoCotizacion.partidas">
                                                     <td style="text-align:center; vertical-align:inherit;">{{i+1}}</td>
                                                     <th style="text-align: left;">{{partida.material}}</th>
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
                     </div>
                     <div class="modal-footer">
                         <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times-circle"></i>Cerrar</button>
                     </div>
                 </div>
             </div>
        </div>
    </span>
</template>

<script>
    import EncabezadoSolicitudCompra from "../solicitud-compra/partials/Encabezado";
    export default {
        name: "consulta-estado",
        components: {EncabezadoSolicitudCompra},
        props: ['id'],
        data() {
            return {
                cargando: false,
            }
        },
        methods: {
            find() {
                this.cargando = true;
                $(this.$refs.modal).appendTo('body');
                $(this.$refs.modal).modal('show');
                this.$store.commit('compras/solicitud-compra/SET_SOLICITUD', null);
                return this.$store.dispatch('compras/solicitud-compra/find', {
                    id: this.id,
                    params:{include: ['detalleEstadoCotizacion']}
                }).then(data => {
                    this.$store.commit('compras/solicitud-compra/SET_SOLICITUD', data);
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
