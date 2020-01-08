<template>
    <span>
        <button @click="find(id)" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </button>
        <div class="modal fade" ref="modal">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> CONSULTA DE PROVEEDOR / CONTRATISTA</h5>
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
                                            <div class="col-12">
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
                                                                        <th class="align">Tipo Proveedor y/o Contratista:</th>
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
                                            <div class="col-12" v-if="proveedorContratista">
                                                <div class="invoice p-3 mb-3">
                                                    <div class="row" v-if="proveedorContratista.sucursales">
                                                        <div class="table-responsive col-12">
                                                            <table class="table table-striped">
                                                                <thead>
                                                                    <tr>
                                                                        <th>#</th>
                                                                        <th>Descripción</th>
                                                                        <th>Dirección</th>
                                                                        <th>Ciudad</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr v-for="(sucursal, i) in proveedorContratista.sucursales.data">
                                                                        <td>{{i+1}}</td>
                                                                        <td>{{sucursal.descripcion}}</td>
                                                                        <td>{{sucursal.direccion}}</td>
                                                                        <td>{{sucursal.ciudad}}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div aria-labelledby="nav-materiales-tab" class="tab-pane fade" id="nav-materiales" role="tabpanel"  style="display:block;">
                                            <div class="col-12" v-if="proveedorContratista">
                                                <div class="invoice p-3 mb-3">
                                                    <div class="row" v-if="proveedorContratista.suministrados">
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
                                    </div>
                                    
                                </nav> 
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" @click="closeModal">Cerrar</button>
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
    data(){
        return {
        }
    },
    methods: {
        find(id) {
            this.$store.commit('cadeco/proveedor-contratista/SET_PROVEEDOR_CONTRATISTA', null);
            return this.$store.dispatch('cadeco/proveedor-contratista/find', {
                id: id,
                params: {include: ['suministrados.material']}
            }).then(data => {
                console.log(data);
                this.$store.commit('cadeco/proveedor-contratista/SET_PROVEEDOR_CONTRATISTA', data);
                $(this.$parent.$refs.modal).modal('show');
            })
        },
        closeModal(){
            // $(this.$refs.modal).modal('clear');
            // $(this.$refs.modal).modal('hide');
            // $(this.$parent.$refs.modal).modal('hide');
        }
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