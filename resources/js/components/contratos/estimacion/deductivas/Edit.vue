<template>
    <span>
        <div class="row">
            <div class="col-md-12">
                 <button type="button" @click="init()" class="btn btn-primary float-right" title="Editar">
                    Deductivas
                </button>
            </div>
            <div class="col-md-12">
                <div class="modal fade" ref="modalIndex" data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle"> 
                                    <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                    <i class="fa fa-edit" v-else></i>Editar Deductivas</h5>
                                <button type="button" class="close" @click="cerrar()" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form role="form" @submit.prevent="validate">
                            <div class="modal-body" style="height:550px;">
                                <div class="col-12">
                                    <DeductivaCreate @created="updateDescuento" v-bind:id="id" v-bind:id_empresa="id_empresa"></DeductivaCreate>
                                </div>
                                <div class="col-12 table-responsive" v-if="descuentos.length > 0">
                                    <table class="table table-striped  table-fixed-suc fixed">
                                        <thead>
                                            <tr>
                                                <th  rowspan="2">Concepto</th>
                                                <!-- <th colspan="4">Descuento Total</th>
                                                <th colspan="2">Descuento Acumulado</th>
                                                <th colspan="2">Por Descontar</th> -->
                                                <th colspan="4">Descuento Actual</th>
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
                                                <th style="width:10%;">Unidad</th>
                                                <th style="width:20%;">Cantidad</th>
                                                <th style="width:15%;">P.U.</th>
                                                <th style="width:20%;">Importe</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(descuento, i) in descuentos">
                                            <td style="width:280px;">{{descuento.material.descripcion}}</td>
                                            <td style="width:70px;">{{descuento.material.unidad}}</td>
                                            <!-- <td style="width:10%;">{{descuento.cantidad_format}}</td> -->
                                            <td style="width:130px;"><input type="number" step="any" :name="`cantidad[${i}]`" 
                                                        data-vv-as="Cantidad"  v-validate="{required:true, min_value:0, decimal:5}" :class="{'is-invalid': errors.has(`cantidad[${i}]`)}"
                                                        class="form-control float-right align" id="cantidad" placeholder="Cantidad"
                                                        v-model="descuento.cantidad">
                                                <div class="invalid-feedback" v-show="errors.has(`cantidad[${i}]`)">{{ errors.first(`cantidad[${i}]`) }}</div>
                                            </td>
                                            <td style="width:130px;" class="align"><input type="number" step="any" :name="`precio[${i}]`" 
                                                        data-vv-as="Precio"  v-validate="{required:true, min_value:0.01, decimal:4}"
                                                        class="form-control float-right align" id="precio" placeholder="Precio"
                                                        v-model="descuento.precio" :class="{'is-invalid': errors.has(`precio[${i}]`)}">
                                                <div class="invalid-feedback" v-show="errors.has(`precio[${i}]`)">{{ errors.first(`precio[${i}]`) }}</div>
                                            </td>
                                            <td style="width:140px;" class="align">$ {{importe(descuento)}}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" @click="cerrar()">Cerrar</button>
                                <button type="submit" class="btn btn-primary"  v-if="$root.can(['registrar_descuento_estimacion_subcontrato','eliminar_descuento_estimacion_subcontrato'])">Actualizar</button>
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
import DeductivaCreate from './Create';
export default {
    name: "deductiva-edit",
    components: {DeductivaCreate},
    props: ['id', 'id_empresa'],
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