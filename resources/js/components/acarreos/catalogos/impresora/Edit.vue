<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-info" title="Editar">
            <i class="fa fa-pencil"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-plus"></i> EDITAR IMPRESORA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div v-if="!impresora">
                                <div class="row" >
                                    <div class="col-md-12">
                                        <div class="spinner-border text-success" role="status">
                                            <span class="sr-only">Cargando...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="form-group col-md-12 error-content">
                                            <label for="mac" class="col-form-label">MAC:</label>
                                            <input disabled="true"
                                                   name="mac"
                                                   class="form-control"
                                                   id="mac"
                                                   v-model="impresora.mac"/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-12 error-content">
                                            <label for="marca" class="col-form-label">Marca:</label>
                                            <input  type="text"
                                                    name="marca"
                                                    data-vv-as="'Marca'"
                                                    v-validate="{required: true, min:6}"
                                                    class="form-control"
                                                    id="marca"
                                                    v-model="impresora.marca"
                                                    :class="{'is-invalid': errors.has('marca')}" />
                                            <div class="invalid-feedback" v-show="errors.has('marca')">{{ errors.first('marca') }}</div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-12 error-content">
                                            <label for="modelo" class="col-form-label">Modelo:</label>
                                            <input  type="text"
                                                    name="modelo"
                                                    data-vv-as="'Modelo'"
                                                    v-validate="{required: true, min:6}"
                                                    class="form-control"
                                                    id="modelo"
                                                    v-model="impresora.modelo"
                                                    :class="{'is-invalid': errors.has('modelo')}" />
                                            <div class="invalid-feedback" v-show="errors.has('modelo')">{{ errors.first('modelo') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0" @click="validate" v-if="impresora"><i class="fa fa-save"></i> Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "impresora-edit",
        props: ['id'],
        data() {
            return {
                impresora : null
            }
        },
        methods: {
            salir() {
                $(this.$refs.modal).modal('hide');
            },
            find() {
                this.impresora = null;
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
                return this.$store.dispatch('acarreos/impresora/find', {
                    id: this.id,
                    params: {}
                }).then(data => {
                    this.impresora = data
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
                return this.$store.dispatch('acarreos/impresora/update', {
                    id: this.id,
                    data: this.impresora
                })
                    .then((data) => {
                        this.$store.commit('acarreos/impresora/UPDATE_IMPRESORA', data);
                        this.salir()
                    })
            },
        }
    }
</script>

<style scoped>

</style>
