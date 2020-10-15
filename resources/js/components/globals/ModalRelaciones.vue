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
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import Relaciones from './TimeLine';
export default {
    name: "ModalRelaciones",
    components:{Relaciones},
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
            if(this.transaccion.tipo == 65){
                this.factura();
            }
            if(this.transaccion.tipo == 82){
                this.pago();
            }
            if(this.transaccion.tipo == 666){
                this.poliza();
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
        }
    },
}
</script>

<style scoped>

</style>
