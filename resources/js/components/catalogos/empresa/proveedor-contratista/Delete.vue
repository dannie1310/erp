<template>
    <span>
        <button type="button" @click="find()" class="btn btn-sm btn-outline-danger" title="Eliminar">
            <i class="fa fa-trash"></i>
        </button>
        <div class="modal fade" ref="modalDelete" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-trash"></i> ELIMINAR PROVEEDOR / CONTRATISTA</h5>
                        <button type="button" class="close" @click="closeModal()" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
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
                                            <tr v-if="proveedorContratista.efo">
                                                <th class="align">Efo:</th>
                                                <td><small v-if="proveedorContratista.efo.estado.id == 2 || proveedorContratista.efo.estado.id == 0" class="badge" 
                                                    :class="{'badge-warning': proveedorContratista.efo.estado.id == 2, 'badge-danger' 
                                                    : proveedorContratista.efo.estado.id == 0 }">
                                                    {{proveedorContratista.efo.estado.descripcion}}
                                                </small></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="closeModal()">Cerrar</button>
                        <button type="button" class="btn btn-danger" @click="eliminar()">Eliminar</button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
export default {
    name: "proveedor-contratista-delete",
    props: ['id'],
    components: {},
    data(){
        return {
            proveedorContratista:[]
        }
    },
    methods: {
        closeModal(){
            $(this.$refs.modalDelete).modal('hide');
        },
        eliminar(){
            return this.$store.dispatch('cadeco/proveedor-contratista/delete', this.id)
                .then(() => {
                    this.$store.commit('cadeco/proveedor-contratista/DELETE_PROVEEDOR_CONTRATISTA', this.id);
                    this.closeModal();
                })
        },
        find() {
            this.proveedorContratista = [];
            return this.$store.dispatch('cadeco/proveedor-contratista/find', {
                id: this.id,
                params: {include: ['suministrados', 'sucursales']}
            }).then(data => {
                this.proveedorContratista = data;
                $(this.$refs.modalDelete).modal('show');
            }).finally(()=>{
                this.cargando=false;
            })
        },
    },

    computed: {
        // proveedorContratista() {
        //     return this.$store.getters['cadeco/proveedor-contratista/currentProveeedor'];
        // }
    },
    watch:{
        // tipo(value){
        //     if(value !== '' && value === 1){
        //         $(this.$refs.modalDelete).modal('show');
        //     }
        // }
    }
}
</script>

<style>

</style>