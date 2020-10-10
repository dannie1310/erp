<template>
    <span>
        <div v-if="relaciones">
            <div class="row">
                <div class="col-md-12">
                    <div class="timeline">
                        <template v-for="(relacion, i) in relaciones">
                            <div class="time-label" v-if="i==0">
                                <span class="bg-gray">{{relacion.fecha}}</span>
                            </div>
                            <div class="time-label" v-else-if="relacion.fecha != relaciones[i-1].fecha" >
                                <span class="bg-gray">{{relacion.fecha}}</span>
                            </div>
                            <div>
                                <i class="bg-green" :class="relacion.icono" v-if="relacion.consulta==1"></i>
                                <i class="bg-blue" :class="relacion.icono" v-else></i>
                                <div class="timeline-item">
                                    <span class="time"><i class="fas fa-clock"></i> {{relacion.hora}}</span>
                                    <h3 class="timeline-header">Registro de {{relacion.tipo}} {{relacion.numero_folio}} por {{relacion.usuario}}</h3>
                                    <div class="timeline-body">
                                        {{relacion.observaciones}}
                                    </div>
                                    <div class="timeline-footer">
                                        <Solicitudes  v-bind:value="relacion" v-if="relacion.tipo_numero==17"></Solicitudes>
                                        <Cotizaciones  v-bind:value="relacion" v-if="relacion.tipo_numero==18"></Cotizaciones>
                                        <OrdenesCompra  v-bind:value="relacion" v-if="relacion.tipo_numero==19"></OrdenesCompra>
                                        <Entradas  v-bind:value="relacion" v-if="relacion.tipo_numero==33"></Entradas>
                                        <Facturas  v-bind:value="relacion" v-if="relacion.tipo_numero==65"></Facturas>
                                        <Salidas  v-bind:value="relacion" v-if="relacion.tipo_numero==34"></Salidas>
                                        <ContratosProyectados  v-bind:value="relacion" v-if="relacion.tipo_numero==49"></ContratosProyectados>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import Cotizaciones from '../compras/cotizacion/partials/ActionButtonsConsulta';
import Solicitudes from '../compras/solicitud-compra/partials/ActionButtonsConsulta';
import OrdenesCompra from '../compras/orden-compra/partials/ActionButtonsConsulta';
import Entradas from '../almacenes/entrada-almacen/partials/ActionButtonsConsulta';
import Facturas from '../finanzas/factura/partials/ActionButtonsConsulta';
import Salidas from '../almacenes/salida-almacen/partials/ActionButtonsConsulta';
import ContratosProyectados from '../contratos/proyectado/partials/ActionButtonsConsulta';
export default {
    name: "Timeline",
    components:{Cotizaciones, Solicitudes, OrdenesCompra, Entradas, Facturas, Salidas, ContratosProyectados},
    props: ['relaciones'],
    data(){
        return{
            cargando_relaciones: false,
            configuracion: '',
            fecha:'',
        }
    },
}
</script>

<style scoped>

</style>
