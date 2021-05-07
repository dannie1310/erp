<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_origen')" class="btn btn-app btn-info pull-right">
            <i class="fa fa-plus"></i> Registrar
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-plus"></i> REGISTRAR MATERIAL</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-md-12 error-content">
                                    <label for="descripcion" class="col-form-label">Descripción:</label>
                                    <input style="text-transform:uppercase;"
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
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0" @click="validate"><i class="fa fa-save"></i> Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "Create",
        data() {
            return {
                descripcion : '',
            }
        },
        mounted() {
            this.$validator.reset();
        },
        methods : {
            init() {
                this.descripcion = '';
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            },
            validate() {
                this.$validator.validate().then(result => {
                    this.descripcion = this.descripcion.toUpperCase();
                    if (result) {
                        this.store();
                    }
                });
            },
            store() {
                return this.$store.dispatch('acarreos/material/store', {
                    Descripcion: this.descripcion,
                })
                .then((data) => {
                    $(this.$refs.modal).modal('hide');
                    this.$emit('created', data)
                });
            },
        }
    }
</script>

<style scoped>

</style>
