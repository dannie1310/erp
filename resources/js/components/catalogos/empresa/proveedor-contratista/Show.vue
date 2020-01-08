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
                        <nav>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><b>Banco:</b></label>
                                    BANCO
                                </div>
                            </div>
                            <nav>
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    <a aria-controls="nav-home" class="nav-item nav-link active" data-toggle="tab" href="#nav-home"
                                    id="nav-home-tab" role="tab">Identificación</a>
                                    <a aria-controls="nav-profile"  class="nav-item nav-link" data-toggle="tab" href="#nav-profile"
                                     id="nav-profile-tab" role="tab">Cuentas</a>
                                    <!-- <a aria-controls="nav-contact" aria-selected="false" class="nav-item nav-link" data-toggle="tab" href="#nav-contact"
                                    id="nav-contact-tab" role="tab">Sucursales</a> -->
                                </div>
                            </nav>

                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="nav-home" >
                                    polar {{id}}
                                </div>
                                <div class="tab-pane fade" id="nav-profile">
                                    Panda {{id}}
                                </div>
                                <!-- <div aria-labelledby="nav-contact-tab" class="tab-pane fade" id="nav-contact" role="tabpanel" style="display:block;">
                                    Pardo {{id}}
                                </div> -->
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
        methods: {
            find(id) {
                this.$store.commit('cadeco/proveedor-contratista/SET_PROVEEDOR_CONTRATISTA', null);
                return this.$store.dispatch('cadeco/proveedor-contratista/find', {
                    id: id,
                    params: {include: ['suministrados.material']}
                }).then(data => {
                    console.log(data);
                    this.$store.commit('cadeco/proveedor-contratista/SET_PROVEEDOR_CONTRATISTA', data);
                    // $(this.$refs.modal).draggable();
                    $(this.$refs.modal).modal('show');
                })
            },
            closeModal(){
                // $(this.$refs.modal).modal('clear');
                $(this.$refs.modal).modal('hide');
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