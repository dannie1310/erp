<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </button>
        <!-- Modal -->
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">CONSULTA DEL ALMACÉN</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" v-if="almacen" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="tipo" class="col-md-2 col-form-label">Tipo: </label>
                                        <div class="col-md-10">
                                            <input type="text"
                                                disabled="true"
                                                name="tipo"
                                                style="text-transform:uppercase;"
                                                data-vv-as="Tipo"
                                                v-validate="{required: true}"
                                                class="form-control float-right"
                                                id="tipo"
                                                placeholder="Tipo"
                                                v-model="almacen.tipo">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="descripcion" class="col-md-2 col-form-label">Descripción: </label>
                                        <div class="col-md-10">
                                            <input type="text"
                                                   disabled="true"
                                                   name="descripcion"
                                                   style="text-transform:uppercase;"
                                                   data-vv-as="Descripción"
                                                   class="form-control float-right"
                                                   id="descripcion"
                                                   placeholder="Descripción"
                                                   v-model="almacen.descripcion">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" v-if="almacen.material">
                                    <div class="form-group row error-content">
                                        <label class="col-md-2 col-form-label">Insumos:</label>
                                        <div class="col-md-10">
                                            <input type="text"
                                                   disabled="true"
                                                   name="material"
                                                   data-vv-as="Material"
                                                   id="material"
                                                   class="form-control float-right"
                                                   v-model="almacen.material.descripcion">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" v-if="almacen.numero_economico">
                                    <div class="form-group row error-content">
                                        <label for="numero_economico" class="col-md-2 col-form-label">Número de Economico: </label>
                                        <div class="col-md-10">
                                            <input type="text"
                                                   disabled="true"
                                                   name="numero_economico"
                                                   style="text-transform:uppercase;"
                                                   data-vv-as="Número de Economico"
                                                   class="form-control float-right"
                                                   id="numero_economico"
                                                   placeholder="Número de Economico"
                                                   v-model="almacen.numero_economico">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" v-if="almacen.clasificacion">
                                    <div class="form-group row error-content">
                                        <label for="clasificacion" class="col-md-2 col-form-label">Clasificación:</label>
                                        <div class="col-md-10">
                                            <input type="text"
                                                   disabled="true"
                                                   name="clasificacion"
                                                   style="text-transform:uppercase;"
                                                   data-vv-as="clasificacion"
                                                   class="form-control float-right"
                                                   id="clasificacion"
                                                   placeholder="Clasificación"
                                                   v-model="almacen.clasificacion">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" v-if="almacen.propiedad">
                                    <div class="form-group row error-content">
                                        <label for="propiedad" class="col-md-2 col-form-label">Propiedad:</label>
                                        <div class="col-md-10">
                                            <input type="text"
                                                   disabled="true"
                                                   name="propiedad"
                                                   style="text-transform:uppercase;"
                                                   data-vv-as="propiedad"
                                                   class="form-control float-right"
                                                   id="propiedad"
                                                   placeholder="propiedad"
                                                   v-model="almacen.propiedad">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" v-on:click="salir">
                                <i class="fa fa-angle-left"></i>Regresar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "almacen-show",
        props: ['id'],
        data() {
            return {
                cargando : false,
                almacen : [],
            }
        },
        methods: {
            find() {
                this.cargando = true;
                return this.$store.dispatch('cadeco/almacen/find', {
                    id: this.id,
                    params: {include: 'material'}
                })
                    .then(data => {
                        this.almacen = data
                        $(this.$refs.modal).appendTo('body')
                        $(this.$refs.modal).modal('show');
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            salir(){
                $(this.$refs.modal).modal('hide');
            },
        },
    }
</script>

<style scoped>

</style>
