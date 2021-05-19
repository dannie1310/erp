<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-info" title="Editar">
            <i class="fa fa-pencil"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-pencil"></i> EDITAR MARCA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div v-if="cargando">
                                <div class="row" >
                                    <div class="col-md-12">
                                        <div class="spinner-border text-success" role="status">
                                            <span class="sr-only">Cargando...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else>
                                <div class="row">
                                    <div class="form-group col-md-12 error-content">
                                        <label for="descripcion" class="col-form-label">Descripción:</label>
                                        <input
                                            style="text-transform:uppercase;"
                                            type="text"
                                            name="descripcion"
                                            data-vv-as="'Descripción'"
                                            v-validate="{required: true, min:6}"
                                            class="form-control"
                                            id="descripcion"
                                            v-model="descripcion"
                                            :class="{'is-invalid': errors.has('descripcion')}" />
                                        <div class="invalid-feedback" v-show="errors.has('descripcion')">{{ errors.first('descripcion') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
                            <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0" @click="validate" v-if="!cargando"><i class="fa fa-save"></i> Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
export default {
    name: "edit-marca",
        data() {
            return {
                cargando: true,
                descripcion: '',
            }
        },
        methods : {
            find() {
                this.cargando = true;
                this.descripcion= '';
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
                return this.$store.dispatch('acarreos/marca/find', {
                    id: this.id,
                    params: {}
                }).then(data => {
                    this.descripcion = data.descripcion
                    this.cargando = false;
                })
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.update()
                    }
                });
            },
            update() {
                var datos = {
                    'Descripcion' : this.descripcion,
                }
                return this.$store.dispatch('acarreos/marca/update', {
                    id: this.id,
                    data: datos
                })
                .then((data) => {
                    this.$store.commit('acarreos/marca/UPDATE_MARCA', data);
                    $(this.$refs.modal).modal('hide');
                })
            },
        }

}
</script>

<style>

</style>