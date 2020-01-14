<template>
    <span>
        <div class="row" v-if="$root.can('editar_sucursal_proveedor')" style="height:350px;" >
            <div class="col-12" v-if="sucursales">
                <div class="invoice p-3 mb-3">
                    <div class="row">
                        <div class="table-responsive col-12">
                            <table class="table table-striped table-fixed">
                                <thead>
                                    <tr>
                                        <th style="width:5%;">#</th>
                                        <th style="width:30%;">Descripción</th>
                                        <th style="width:30%;">Dirección</th>
                                        <th style="width:15%;">Ciudad</th>
                                        <th style="width:20%;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(sucursal, i) in sucursales">
                                        <td style="width:5%;">{{i+1}}</td>
                                        <td style="width:30%;">{{sucursal.descripcion}}</td>
                                        <td style="width:30%;">{{sucursal.direccion}}</td>
                                        <td style="width:15%;">{{sucursal.ciudad}}</td>
                                        <td style="width:20%;">
                                            <show-sucursal v-bind:id="sucursal.id"></show-sucursal>
                                            <button type="button" class="btn btn-sm btn-outline-danger" @click="deleteSucursal(sucursal.id)" title="Eliminar">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                            <edit-sucursal @created="updateSucursal" v-bind:id="sucursal.id"></edit-sucursal>
                                            
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <create-sucursal @created="updateSucursal" v-bind:id="id_empresa"></create-sucursal>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import EditSucursal from '../partials/EditSucursal';
import CreateSucursal from '../partials/CreateSucursal'; 
import ShowSucursal from '../partials/ShowSucursal'; 
export default {
    name: "proveedor-contratista-sucursal-tab",
    components: {CreateSucursal, EditSucursal, ShowSucursal},
    props: ['id_empresa'],
    data(){
        return {
        }
    },
    methods: {
        deleteSucursal(id) {
            if(this.sucursales.length === 1){
                swal('¡Aviso!', 'El Proveedor / Contratista debe tener al menos una sucursal registrada.', 'warning')
            }else{
                return this.$store.dispatch('cadeco/sucursal/delete', id)
                    .then(() => {
                        this.$store.commit('cadeco/sucursal/DELETE_SUCURSAL', id)
                    })
            }
        },
        updateSucursal(data){
            this.$store.commit('cadeco/sucursal/INSERT_SUCURSAL', data);
        },
    },
    computed: {
        sucursales(){
            return this.$store.getters['cadeco/sucursal/sucursales'];
        }
    },

}
</script>

<style>
.align{
    text-align: left;
}
.table-fixed tbody {
    display:block;
    height:218px;
    overflow:auto;
}
.table-fixed thead, .table-fixed tbody tr {
    display:table;
    width:100%;
    text-align: start;
}
</style>