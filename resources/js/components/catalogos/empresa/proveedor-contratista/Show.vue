<template>
    <span>
        <button @click="find(id)" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> CONSULTA DE PROVEEDOR / CONTRATISTA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row" v-if="proveedorContratista">
                            <!-- <nav> -->
                                <div class="col-12">
                                    <div class="invoice p-3 mb-3">
                                        <div class="row">
                                            <div class="table-responsive col-12">
                                                <table class="table table-striped">
                                                    <tbody>
                                                        <tr>
                                                            <th>Razón Social:</th>
                                                            <td>{{proveedorContratista.razon_social}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>R.F.C.:</th>
                                                            <td>{{proveedorContratista.rfc}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>No. de Proveedor Virtual:</th>
                                                            <td>{{proveedorContratista.proveedor_virtual}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Días de Crédito:</th>
                                                            <td>{{proveedorContratista.dias_credito}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Descuento Financiero:</th>
                                                            <td>{{proveedorContratista.porcentaje}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Tipo Proveedor y/o Contratista:</th>
                                                            <td>{{proveedorContratista.tipo}}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- <nav>
                                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                        <a aria-controls="nav-home" aria-selected="true" class="nav-item nav-link active" data-toggle="tab" href="#nav-home"
                                        id="nav-home-tab" role="tab">Sucursales</a>
                                        <a aria-controls="nav-profile" aria-selected="false" class="nav-item nav-link" data-toggle="tab"
                                        href="#nav-profile" id="nav-profile-tab" role="tab" >Materiales Suministrados</a>
                                    </div>
                                </nav>
                                <div class="tab-content" id="nav-tabContent">
                                    <div aria-labelledby="nav-home-tab" class="tab-pane fade show active" id="nav-home" role="tabpanel">
                                        <div class="col-12">
                                            <div class="invoice p-3 mb-3">
                                                <div class="row">
                                                    <div class="table-responsive col-12">
                                                        <table class="table table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Material</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr v-for="(material, i) in proveedorContratista.suministrados.data">
                                                                    <td>{{i+1}}</td>
                                                                    <td>{{material.material.descripcion}}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>    
                                    </div>
                                    <div aria-labelledby="nav-profile-tab" class="tab-pane fade" id="nav-profile" role="tabpanel">
                                    
                                    </div>
                                </div> -->
                                
                            <!-- </nav> -->

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
export default {
    name: "solicitud-alta-show",
        props: ['id'],
        methods: {
            find(id) {
                this.$store.commit('cadeco/proveedor-contratista/SET_PROVEEDOR_CONTRATISTA', null);
                return this.$store.dispatch('cadeco/proveedor-contratista/find', {
                    id: id,
                    params: {include: ['suministrados.material']}
                }).then(data => {
                    console.log(data);
                    this.$store.commit('cadeco/proveedor-contratista/SET_PROVEEDOR_CONTRATISTA', data);
                    $(this.$refs.modal).draggable();
                    $(this.$refs.modal).modal('show');
                })
            },
        },
        computed: {
            proveedorContratista() {
                return this.$store.getters['cadeco/proveedor-contratista/currentProveeedor'];
            }
        }

}
</script>

<style>

</style>