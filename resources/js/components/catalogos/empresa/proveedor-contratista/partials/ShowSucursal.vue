<template>
  <span>
        <button @click="find(id)" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </button>
        <div class="modal fade" ref="modalShowSucursal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-eye"></i> CONSULTA DE SUCURSAL</h5>
                        <button type="button" class="close" @click="closeModal()" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row" v-if="sucursal">
                            <!---Sucursal-->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><b>Descripción: </b></label>
                                    {{ sucursal.descripcion }}
                                </div>
                            </div>
                            <!--Ubicación -->
                            <div class="col-md-12 text-left" >
                                <label class="text-secondary">Ubicación </label>
                                <hr style="color: #0056b2; margin-top:auto;" width="95%" size="10" />
                            </div>
                            <!--Dirección-->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><b>Dirección: </b></label>
                                    {{ sucursal.direccion }}
                                </div>
                            </div>
                            <!--Ciudad-->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><b>Ciudad: </b></label>
                                    {{ sucursal.ciudad }}
                                </div>
                            </div>
                            <!--Código Postal-->
                                <div class="col-md-6">
                                <div class="form-group">
                                    <label><b>C.P.: </b></label>
                                    {{ sucursal.codigo_postal }}
                                </div>
                            </div>
                            <!--Estado-->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><b>Estado: </b></label>
                                    {{ sucursal.estado }}
                                </div>
                            </div>

                            <!--Teléfonos -->
                            <div class="col-md-12 text-left" >
                                <label class="text-secondary">Teléfonos</label>
                                <hr style="color: #0056b2; margin-top:auto;" width="95%" size="10" />
                            </div>

                            <!--Voz-->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><b>Voz: </b></label>
                                    {{ sucursal.telefono }}
                                </div>
                            </div>
                            <!--Fax-->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><b>Fax: </b></label>
                                    {{ sucursal.fax }}
                                </div>
                            </div>
                            <!--Móvil-->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><b>Móvil: </b></label>
                                    {{ sucursal.telefono_movil }}
                                </div>
                            </div>
                            <div class="col-md-6"></div>
                            <!--Contacto -->
                            <div class="col-md-12 text-left" >
                                <label class="text-secondary">Contacto</label>
                                <hr style="color: #0056b2; margin-top:auto;" width="95%" size="10" />
                            </div>
                            <!--Contacto-->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><b>Contacto: </b></label>
                                    {{ sucursal.contacto}}
                                </div>
                            </div>
                            <!--Cargo-->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><b>Cargo: </b></label>
                                    {{ sucursal.cargo }}
                                </div>
                            </div>
                            <!--E-Mail-->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><b>E-Mail: </b></label>
                                    {{ sucursal.email }}
                                </div>
                            </div>
                            <!--Observaciones-->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><b>Observaciones: </b></label>
                                    {{ sucursal.observaciones }}
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
export default {
        name: "proveedor-sucursal-show",
        props: ['id'],
        data(){
            return{
                sucursal:[],
                cargando:false
            }
        },
        methods: {
            closeModal(){
                $(this.$refs.modalShowSucursal).modal('hide');
            },
            find(id){
                this.cargando = true;
                return this.$store.dispatch('cadeco/proveedor-contratista-sucursal/find', {
                    id: id
                })
                .then(data => {
                    this.sucursal = data;
                })
                .finally(() => {
                    this.cargando = false;
                    $(this.$refs.modalShowSucursal).appendTo('body');
                    $(this.$refs.modalShowSucursal).modal('show');
                })
            },
        },
        computed: {
        }
    }
</script>

<style>

</style>
