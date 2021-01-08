<template>
    <span>
        <button @click="find()" type="button" class="btn btn-sm btn-outline-success" :disabled="cargando_relaciones" title="Ver Relaciones">
            <i class="fa fa-project-diagram" v-if="!cargando_relaciones"></i>
            <i class="fa fa-spinner fa-spin" v-else></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-project-diagram"></i> Transacciones Relacionadas</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>

                    </div>
                    <div class="modal-body" style="height: 800px; overflow-y: scroll" >
                        <Relaciones v-bind:relaciones="relaciones" v-if="relaciones"></Relaciones>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fa fa-times-circle"></i>
                            Cerrar
                        </button>
                        <ModalArchivos v-bind:relacionados="true" v-bind:id="transaccion.tipo+'/'+transaccion.id" v-bind:url="'/sao/modal/lista_archivos_relacionados/{id}'" ></ModalArchivos>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import Relaciones from './TimeLine';
import ModalArchivos from './archivos/Modal';
export default {
    name: "ModalRelaciones",
    components:{Relaciones, ModalArchivos},
    props: ['transaccion'],
    data(){
        return{
            cargando_relaciones: false,
            configuracion: '',
            fecha:'',
            relaciones:null
        }
    },
    methods: {
        find() {
            this.cargando_relaciones = true;
            if(this.transaccion.tipo == 65){
                this.factura();
            }
            if(this.transaccion.tipo == 82){
                this.pago();
            }
            if(this.transaccion.tipo == 666){
                this.poliza();
            }
            if(this.transaccion.tipo == 17){
                this.solicitud();
            }
            if(this.transaccion.tipo == 18){
                this.cotizacion();
            }
            if(this.transaccion.tipo == 19){
                this.ordenCompra();
            }
            if(this.transaccion.tipo == 33){
                this.entrada();
            }
            if(this.transaccion.tipo == 49){
                this.contratoProyectado();
            }
            if(this.transaccion.tipo == 50){
                this.presupuesto();
            }
            if(this.transaccion.tipo == 51){
                this.subcontrato();
            }
            if(this.transaccion.tipo == 52){
                this.estimacion();
            }
            if(this.transaccion.tipo == 72){
                this.solicitud_pago_anticipado();
            }
            if(this.transaccion.tipo == 54){
                this.solicitud_cambio_subcontrato();
            }
            $(this.$refs.modal).appendTo('body')
            $(this.$refs.modal).modal('show')
        },
        factura(){
            return this.$store.dispatch('finanzas/factura/find', {
                id: this.transaccion.id,
                params:{include: [
                        'relaciones'
                    ]}
            }).then(data => {
                this.relaciones = data.relaciones.data
            })
                .finally(()=> {
                    this.cargando_relaciones = false;
                });
        },
        pago(){
            return this.$store.dispatch('finanzas/pago/find', {
                id: this.transaccion.id,
                params:{include: [
                        'relaciones'
                    ]}
            }).then(data => {
                this.relaciones = data.relaciones.data
            })
                .finally(()=> {
                    this.cargando_relaciones = false;
                });
        },
        poliza(){
            return this.$store.dispatch('contabilidad/poliza/find', {
                id: this.transaccion.id,
                params:{include: [
                        'relaciones'
                    ]}
            }).then(data => {
                this.relaciones = data.relaciones.data
            })
                .finally(()=> {
                    this.cargando_relaciones = false;
                });
        },
        solicitud(){
            return this.$store.dispatch('compras/solicitud-compra/find', {
                id: this.transaccion.id,
                params:{include: [
                        'relaciones'
                    ]}
            }).then(data => {
                this.relaciones = data.relaciones.data
            })
                .finally(()=> {
                    this.cargando_relaciones = false;
                });
        },
        cotizacion(){
            return this.$store.dispatch('compras/cotizacion/find', {
                id: this.transaccion.id,
                params:{include: [
                        'relaciones'
                    ]}
            }).then(data => {
                this.relaciones = data.relaciones.data
            })
                .finally(()=> {
                    this.cargando_relaciones = false;
                });
        },
        ordenCompra(){
            return this.$store.dispatch('compras/orden-compra/find', {
                id: this.transaccion.id,
                params:{include: [
                        'relaciones'
                    ]}
            }).then(data => {
                this.relaciones = data.relaciones.data
            })
                .finally(()=> {
                    this.cargando_relaciones = false;
                });
        },
        entrada(){
            return this.$store.dispatch('almacenes/entrada-almacen/find', {
                id: this.transaccion.id,
                params:{include: [
                        'relaciones'
                    ]}
            }).then(data => {
                this.relaciones = data.relaciones.data
            })
                .finally(()=> {
                    this.cargando_relaciones = false;
                });
        },
        contratoProyectado(){
            return this.$store.dispatch('contratos/contrato-proyectado/find', {
                id: this.transaccion.id,
                params:{include: [
                        'relaciones'
                    ]}
            }).then(data => {
                this.relaciones = data.relaciones.data
            })
                .finally(()=> {
                    this.cargando_relaciones = false;
                });
        },
        presupuesto(){
            return this.$store.dispatch('contratos/presupuesto/find', {
                id: this.transaccion.id,
                params:{include: [
                        'relaciones'
                    ]}
            }).then(data => {
                this.relaciones = data.relaciones.data
            })
                .finally(()=> {
                    this.cargando_relaciones = false;
                });
        },
        subcontrato(){
            return this.$store.dispatch('contratos/subcontrato/find', {
                id: this.transaccion.id,
                params:{include: [
                        'relaciones'
                    ]}
            }).then(data => {
                this.relaciones = data.relaciones.data
            })
                .finally(()=> {
                    this.cargando_relaciones = false;
                });
        },
        estimacion(){
            return this.$store.dispatch('contratos/estimacion/find', {
                id: this.transaccion.id,
                params:{include: [
                        'relaciones'
                    ]}
            }).then(data => {
                this.relaciones = data.relaciones.data
            })
                .finally(()=> {
                    this.cargando_relaciones = false;
                });
        },
        solicitud_pago_anticipado(){
            return this.$store.dispatch('finanzas/solicitud-pago-anticipado/find', {
                id: this.transaccion.id,
                params:{include: [
                        'relaciones'
                    ]}
            }).then(data => {
                this.relaciones = data.relaciones.data
            })
                .finally(()=> {
                    this.cargando_relaciones = false;
                });
        },
        solicitud_cambio_subcontrato(){
            return this.$store.dispatch('contratos/solicitud-cambio/find', {
                id: this.transaccion.id,
                params:{include: [
                        'relaciones'
                    ]}
            }).then(data => {
                this.relaciones = data.relaciones.data
            })
                .finally(()=> {
                    this.cargando_relaciones = false;
                });
        }
    },
}
</script>

<style scoped>

</style>
