<template>
    <span>
        <div class="row">
            <div class="col-md-12">
                 <button type="button" @click="init()" class="btn btn-primary float-right" v-if="$root.can(['registrar_descuento_estimacion_subcontrato','eliminar_descuento_estimacion_subcontrato'])" title="Editar">
                    Amortización de Anticipo
                </button>
            </div>
            <div class="col-md-12">
                <div class="modal fade" ref="modalIndex" data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle"> 
                                    <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                    <i class="fa fa-edit" v-else></i>EDITAR AMORTIZACIÓN DE ANTICIPO</h5>
                                <button type="button" class="close" @click="cerrar()" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form role="form" @submit.prevent="validate">
                            <div class="modal-body">
                                <div class="row">
                            <div class="col-12">
                                <div class="invoice p-3 mb-3">
                                    <div class="row">
                                        <div class="col-12">
                                            <b>Folio:</b>
                                            #004746
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="table-responsive col-md-12">
                                            <table class="table table-striped">
                                                <tbody>
                                                    <tr>
                                                        <td class="bg-gray-light" colspan="2"><b>Suma de Importes</b></td>
                                                        <td class="bg-gray-light">$2,314,755.4200</td>                                                        
                                                    </tr>
                                                    <tr>
                                                        <td class="bg-gray-light"><b>Amortización de Anticipo</b></td>
                                                        <td class="bg-gray-light">0.0000%</td>
                                                        <td class="bg-gray-light" style="width: 30%">
                                                            <input v-model="campo"
                                                                   name="campo"
                                                                   class="form-control col-10"
                                                                   id="campo">                                                               
                                                               </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="bg-gray-light" colspan="2"><b>Subtotal</b></td>
                                                        <td class="bg-gray-light">$2,314,755.4200</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="bg-gray-light" colspan="2"><b>I.V.A.</b></td>
                                                        <td class="bg-gray-light">$370,360.8672</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="bg-gray-light" colspan="2"><b>Total</b></td>
                                                        <td class="bg-gray-light"><b>$2,685,116.2872</b></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>                                
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" @click="cerrar()">Cerrar</button>
                                <button type="submit" class="btn btn-primary" :disabled="!campo">Actualizar</button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>

</template>

<script>
// import DeductivaCreate from './Create';
import datepicker from 'vuejs-datepicker';
import {es} from 'vuejs-datepicker/dist/locale';
export default {
    name: "resumen-edit",
    components: {datepicker},
    props: ['id', 'id_empresa'],
    data() {
        return {
            descuentos:[],
            cargando:false,
            campo:0.001
        }
    },
    mounted() {
    },
    methods: {
        cerrar(){
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
                // params: {include: 'material', sort: 'id_descuento', order: 'desc'}
            }).then(data => {
                this.descuentos = data.data;
            })
            .finally(()=>{
                this.cargando = false;
            })
        },
        updateDescuento(data){
            this.descuentos = data.data;
        },
        updateLista(){
            return this.$store.dispatch('subcontratosEstimaciones/descuento/updateList', this.descuentos)
            .then((data) => {
                this.descuentos = [];
                this.descuentos = data.data;
            })
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    this.updateLista();
                }
            });
        },
    }

}
</script>

<style>
.align{
    text-align: right;
}
.table-fixed-suc tbody {
    display:block;
    height:380px;
    overflow:auto;
}
.table-fixed-suc thead, .table-fixed tbody tr {
    display:table;
    width:100%;
    text-align: left;
}
table.fixed { table-layout:fixed; }
table.fixed td { overflow: hidden; }
</style>