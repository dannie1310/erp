<template>
    <span>
        <button type="button" @click="init()" class="btn btn-primary" title="Editar">
            Deductivas
        </button>
        <div class="row">
            <div class="col-md-12">
                <div class="modal fade" ref="modalIndex" data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-edit"></i>Editar Deductivas</h5>
                                <button type="button" class="close" @click="cerrar()" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="col-12">
                                    <DeductivaCreate @created="updateDescuento" v-bind:id="id"></DeductivaCreate>
                                </div>
                                <div class="col-12 table-responsive" v-if="descuentos">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">Concepto</th>
                                                <!-- <th colspan="4">Descuento Total</th>
                                                <th colspan="2">Descuento Acumulado</th>
                                                <th colspan="2">Por Descontar</th> -->
                                                <th colspan="3">Descuento Actual</th>
                                            </tr>
                                            <tr>
                                                <!-- <th>Cantidad</th>
                                                <th>Unidad</th>
                                                <th>P.U.</th>
                                                <th>Importe</th>
                                                <th>Cantidad</th>
                                                <th>Importe</th>
                                                <th>Cantidad</th>
                                                <th>Importe</th> -->
                                                <th>Cantidad</th>
                                                <th>P.U.</th>
                                                <th>Importe</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(descuento, i) in descuentos">
                                            <td>{{descuento.material.descripcion}}</td>
                                            <td>{{descuento.cantidad_format}}</td>
                                            <td>{{descuento.precio_format}}</td>
                                            <td>$ {{importe(descuento)}}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>

</template>

<script>
import DeductivaCreate from './Create';
export default {
    name: "deductiva-index",
    components: {DeductivaCreate},
    props: ['id'],
    data() {
        return {
            descuentos:[],
            cargando:false,
        }
    },
    mounted() {
    },
    methods: {
        cerrar(){
            // this.material = [];
            // this.cantidad = '';
            // this.precio_unitario = '';
            $(this.$refs.modalIndex).modal('hide');
        },
        importe(desc){
            return parseFloat((desc.cantidad * desc.precio)).formatMoney(2, '.', ',');
        },
        init(){
            this.cargando = true;
            this.lista();
            $(this.$refs.modalIndex).modal('show');
        },
        lista(){
            this.descuentos = [];
            return this.$store.dispatch('subcontratosEstimaciones/descuento/list', {
                id: this.id,
                params: {include: 'material', sort: 'id_descuento', order: 'desc'}
            }).then(data => {
                this.descuentos = data.data;
            })
            .finally(()=>{
                this.cargando = false;
            })
        },
        updateDescuento(data){
            this.descuentos.unshift(data);
            console.log(data);
        },
    }

}
</script>

<style>

</style>