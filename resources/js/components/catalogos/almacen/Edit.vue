<template>
    <span>
        <button @click="find()" type="button" class="btn btn-sm btn-outline-info" title="Editar"  :disabled="cargando">
            <i class="fa fa-pencil" v-if="!cargando"></i>
            <i class="fa fa-spinner fa-spin" v-else></i>
        </button>
        <!-- Modal -->
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">EDICIÓN DEL ALMACÉN</h5>
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
                                                   data-vv-as="Tipo Almacén"
                                                   class="form-control float-right"
                                                   id="tipo"
                                                   placeholder="Tipo Almacén"
                                                   v-model="almacen.tipo"
                                                   :class="{'is-invalid': errors.has('tipo')}">
                                            <div class="invalid-feedback float-right"   v-show="errors.has('tipo')"><span style="margin-left:5%;">{{ errors.first('tipo') }}</span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="descripcion" class="col-md-2 col-form-label">Descripción: </label>
                                        <div class="col-md-10">
                                            <input type="text"
                                                   name="descripcion"
                                                   style="text-transform:uppercase;"
                                                   data-vv-as="Descripción"
                                                   v-validate="{required: true}"
                                                   class="form-control float-right"
                                                   id="descripcion"
                                                   placeholder="Descripción"
                                                   v-model="almacen.descripcion"
                                                   :class="{'is-invalid': errors.has('descripcion')}">
                                            <div class="invalid-feedback float-right"   v-show="errors.has('descripcion')"><span style="margin-left:5%;">{{ errors.first('descripcion') }}</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" v-on:click="salir">
                                <i class="fa fa-angle-left"></i>Regresar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Guardar
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
        name: "almacen-edit",
        props: ['id'],
        data() {
            return {
                cargando : false,
                almacen : []
            }
        },
        methods: {
            find() {
                this.cargando = true;
                return this.$store.dispatch('cadeco/almacen/find', {
                    id: this.id
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
            validate() {
                this.almacen.descripcion = this.almacen.descripcion.toUpperCase()
                this.$validator.validate().then(result => {
                    if (result) {
                        this.update()
                    }
                });
            },
            update() {
                return this.$store.dispatch('cadeco/almacen/update', {
                    id: this.id,
                    data: this.almacen
                }).then(data => {
                    this.$store.commit('cadeco/almacen/UPDATE_ALMACEN', data);
                    this.salir();
                })
            },
            salir(){
                $(this.$refs.modal).modal('hide');
            },
        },
        watch: {

        }
    }
</script>

<style scoped>

</style>
