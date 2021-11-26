<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-primary" title="Ver Estados de Cotizaciones"><i class="fa fa-th-large"></i> </button>
        <div class="modal fade" ref="modal" tabindex="-1" role="dialog" aria-labelledby="modal">
             <div class="modal-dialog modal-xl" id="mdialTamanio">
                 <div class="modal-content">
                     <div class="modal-header">
                         <h4 class="modal-title"><i class="fa fa-th-large"></i> Consulta de estados de cotización</h4>
                         <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
                     </div>
                     <div class="modal-body">
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
                             <div class="row" v-if="contrato">
                                 <div class="col-md-12">
                                     <encabezado-contrato-proyectado v-bind:contrato_proyectado="contrato"></encabezado-contrato-proyectado>
                                 </div>
                             </div>
                             <div class="row"  v-if="contrato">
                                 <div  class="col-md-12">
                                     <div class="table-responsive">
                                         <table class="table table-bordered">
                                             <thead>
                                                 <tr>
                                                     <th style="border-style: none;" colspan="3"></th>
                                                     <th v-for="cotizacion in contrato.detalleEstadoCotizacion.titulos"  v-if="cotizacion.id_transaccion != ''">
                                                         <span v-if="cotizacion.invitacion">Invitación: <br>{{cotizacion.invitacion}} <br v-if="cotizacion.dias_cierre"> <small>{{cotizacion.dias_cierre}}</small> </span>
                                                         <span v-else></span>
                                                     </th>
                                                     <th v-for="cotizacion in contrato.detalleEstadoCotizacion.titulos" v-if="cotizacion.id_transaccion == ''" >
                                                         Invitación: <br> {{cotizacion.invitacion}} <br v-if="cotizacion.dias_cierre"> <small>{{cotizacion.dias_cierre}}</small>
                                                     </th>
                                                 </tr>
                                                 <tr>
                                                     <th style="border-style: none;" colspan="3"></th>
                                                     <th v-for="cotizacion in contrato.detalleEstadoCotizacion.titulos" v-if="cotizacion.id_transaccion != ''">
                                                        Cotización: <br> {{cotizacion.numero_folio}}
                                                     </th>
                                                     <th v-for="cotizacion in contrato.detalleEstadoCotizacion.titulos" v-if="cotizacion.id_transaccion == ''" ></th>
                                                 </tr>
                                                 <tr>
                                                     <th >#</th>
                                                     <th >Clave</th>
                                                     <th >Concepto</th>
                                                     <th v-for="cotizacion in contrato.detalleEstadoCotizacion.titulos" style="padding: 1px;" v-if="cotizacion.id_transaccion != ''" ><b>{{cotizacion.empresa}}</b></th>
                                                     <th v-for="cotizacion in contrato.detalleEstadoCotizacion.titulos" style="padding: 1px;" v-if="cotizacion.id_transaccion == ''" ><b>{{cotizacion.empresa}}</b></th>
                                                 </tr>
                                             </thead>
                                             <tbody>
                                                 <tr v-for="(partida, i) in contrato.detalleEstadoCotizacion.partidas">
                                                     <td style="text-align:center; vertical-align:inherit;">{{i+1}}</td>
                                                     <td style="text-align: left;">{{partida.nivel}}</td>
                                                     <td style="text-align: left;">{{partida.descripcion}}</td>
                                                     <td v-for="p in partida.partidas" style="text-align: center" v-if="p.cotizada != null">
                                                         <i class="fa fa-check" aria-hidden="true" style="color: green" v-if="p.cotizada === true"></i>
                                                         <i class="fa fa-window-minimize" aria-hidden="true" style="color: gray" v-else-if="p.pendiente === true"></i>
                                                         <i class="fa fa-times" aria-hidden="true" style="color: red" v-else></i>
                                                     </td>
                                                     <td v-for="p1 in partida.partidas" style="text-align: center" v-if="p1.cotizada == null" >
                                                         <i class="fa fa-window-minimize" aria-hidden="true" style="color: gray"></i>
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
                         <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i>Cerrar</button>
                     </div>
                 </div>
             </div>
        </div>
    </span>
</template>

<script>
    import EncabezadoContratoProyectado from "../proyectado/Encabezado";
    export default {
        name: "consulta-estado",
        components: {EncabezadoContratoProyectado},
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
                this.$store.commit('contratos/contrato-proyectado/SET_CONTRATO', null);
                return this.$store.dispatch('contratos/contrato-proyectado/find', {
                    id: this.id,
                    params:{include: ['detalleEstadoCotizacion']}
                }).then(data => {
                    this.$store.commit('contratos/contrato-proyectado/SET_CONTRATO', data);
                    this.cargando = false;
                })
            },
        },
        computed: {
            contrato() {
                return this.$store.getters['contratos/contrato-proyectado/currentContrato']
            },
        }
    }
</script>

<style scoped>

</style>
