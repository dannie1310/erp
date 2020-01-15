<template>
    <span>
        <div class="modal fade" ref="modal" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-eye"></i> CONSULTA DE PROVEEDOR / CONTRATISTA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <nav v-if="proveedorContratista">
                            <nav>
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    <a aria-controls="nav-identificacion" aria-selected="true" class="nav-item nav-link active" data-toggle="tab" href="#nav-identificacion"
                                    id="nav-identificacion-tab" role="tab">Identificación</a>

                                    <a aria-controls="nav-sucursales" aria-selected="false" class="nav-item nav-link" data-toggle="tab" href="#nav-sucursales"
                                    id="nav-sucursales-tab" role="tab">Sucursales</a>

                                    <a aria-controls="nav-materiales" aria-selected="false" class="nav-item nav-link" data-toggle="tab" href="#nav-materiales" 
                                    id="nav-materiales-tab" role="tab"  >Materiales Suministrados</a>
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                <div aria-labelledby="nav-identificacion-tab" class="tab-pane fade show active" id="nav-identificacion" role="tabpanel">
                                    <div class="col-12" style="height:350px;">
                                        <div class="invoice p-3 mb-3">
                                            <div class="row">
                                                <div class="table-responsive col-12">
                                                    <table class="table table-striped">
                                                        <tbody>
                                                            <tr>
                                                                <th class="align">Razón Social:</th>
                                                                <td>{{proveedorContratista.razon_social}}</td>
                                                            </tr>
                                                            <tr>
                                                                <th class="align">R.F.C.:</th>
                                                                <td>{{proveedorContratista.rfc}}</td>
                                                            </tr>
                                                            <tr>
                                                                <th class="align">No. de Proveedor Virtual:</th>
                                                                <td>{{proveedorContratista.proveedor_virtual}}</td>
                                                            </tr>
                                                            <tr>
                                                                <th class="align">Días de Crédito:</th>
                                                                <td>{{proveedorContratista.dias_credito}}</td>
                                                            </tr>
                                                            <tr>
                                                                <th class="align">Descuento Financiero:</th>
                                                                <td>{{proveedorContratista.porcentaje}}</td>
                                                            </tr>
                                                            <tr>
                                                                <th class="align">Tipo:</th>
                                                                <td>{{proveedorContratista.tipo}}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div aria-labelledby="nav-sucursales-tab" class="tab-pane fade" id="nav-sucursales" role="tabpanel">
                                    <div class="col-12" v-if="proveedorContratista" style="height:350px;">
                                        <div class="invoice p-3 mb-3">
                                            <div class="row" v-if="proveedorContratista.sucursales">
                                                <div class="table-responsive col-12">
                                                    <table class="table table-striped table-fixed-suc">
                                                        <thead>
                                                            <tr>
                                                                <th style="width:5%;">#</th>
                                                                <th style="width:35%;">Descripción</th>
                                                                <th style="width:35%;">Dirección</th>
                                                                <th style="width:15%;">Ciudad</th>
                                                                <th>Acciones</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(sucursal, i) in proveedorContratista.sucursales.data">
                                                                <td style="width:5%;">{{i+1}}</td>
                                                                <td style="width:35%;">{{sucursal.descripcion}}</td>
                                                                <td style="width:35%;">{{sucursal.direccion}}</td>
                                                                <td style="width:25%;">{{sucursal.ciudad}}</td>
                                                                <td><show-sucursal v-bind:id="sucursal.id"></show-sucursal></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div aria-labelledby="nav-materiales-tab" class="tab-pane fade" id="nav-materiales" role="tabpanel">
                                    <div class="col-12" v-if="proveedorContratista" style="height:350px;">
                                        <div class="invoice p-3 mb-3">
                                            <div class="row" v-if="proveedorContratista.suministrados">
                                                <div class="table-responsive col-12">
                                                    <table class="table table-striped table-fixed-sum">
                                                        <thead>
                                                            <tr>
                                                                <th style="width:5%;">#</th>
                                                                <th style="width:95%;">Material</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(material, i) in proveedorContratista.suministrados.data">
                                                                <td>{{i+1}}</td>
                                                                <td style="width:100%; text-align: left">{{material.material.descripcion}}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>   
                                </div>
                            </div>
                        </nav> 
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="closeModal()">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import ShowSucursal from './partials/ShowSucursal'; 
export default {
    name: "proveedor-contratista-show",
    props: ['tipo'],
    components: {ShowSucursal},
    data(){
        return {
        }
    },
    methods: {
        closeModal(){
            this.$store.commit('cadeco/proveedor-contratista/SET_PROVEEDOR_CONTRATISTA', null);
            $('.nav-tabs a[href="#nav-identificacion"]').tab('show');
            $(this.$refs.modal).modal('hide');
        }
    },

    computed: {
        proveedorContratista() {
            return this.$store.getters['cadeco/proveedor-contratista/currentProveeedor'];
        }
    },
    watch:{
        tipo(value){
            if(value !== '' && value === 1){
                $(this.$refs.modal).modal('show');
            }
        }
    }
    
}
</script>

<style>
.align{
    text-align: left;
}
.table-fixed-suc tbody {
    display:block;
    height:265px;
    overflow:auto;
}
.table-fixed-suc thead, .table-fixed tbody tr {
    display:table;
    width:100%;
    text-align: left;
}
.table-fixed-sum tbody {
    display:block;
    height:265px;
    overflow:auto;
}
.table-fixed-sum thead, .table-fixed tbody tr {
    display:table;
    width:100%;
    text-align: left;
}
</style>