<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_unidad')" class="btn btn-app btn-info float-right" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i>
            Registrar
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-plus"></i> REGISTRAR UNIDAD</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="unidad" class="col-sm-1 col-form-label" style="text-align:right;">Unidad: </label>
                                        <div class="col-sm-2">
                                              <input
                                                type="text"
                                                name="unidad"
                                                data-vv-as="Unidad"
                                                v-validate="{required: true, max:16}"
                                                class="form-control"
                                                id="unidad"
                                                placeholder="######"
                                                v-model="dato.unidad"
                                                :class="{'is-invalid': errors.has('unidad')}">
                                            <div class="invalid-feedback" v-show="errors.has('unidad')">{{ errors.first('unidad') }}</div>
                                        </div>
                                        <label for="descripcion" class="col-sm-2 col-form-label" style="text-align:right;">Descripción: </label>
                                        <div class="col-sm-5">
                                            <input
                                                type="text"
                                                name="descripcion"
                                                data-vv-as="Descripción"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="descripcion"
                                                placeholder="######"
                                                v-model="dato.descripcion"
                                                :class="{'is-invalid': errors.has('descripcion')}">
                                            <div class="invalid-feedback" v-show="errors.has('descripcion')">{{ errors.first('descripcion') }}</div>
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>
                        <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-primary":disabled="errors.count() > 0 ">Registrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "unidad-create",
        data() {
                return {
                    cargando:false,
                    dato: {
                        unidad:'',
                        descripcion: '',
                    }
                }
        },
        methods: {
            init() {
                  this.cargando = false;
                    this.dato.unidad = '';
                    this.dato.descripcion = '';
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            },
            store() {
                return this.$store.dispatch('cadeco/unidad/store', this.$data.dato)
                    .then(data => {
                        this.$emit('created', data);
                        $(this.$refs.modal).modal('hide');
                    }).finally( ()=>{
                        this.cargando = false;
                        this.tipo = '';
                        this.descripcion = '';

                    });
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                            this.store()
                    }
                });
            },
        }
    }
</script>

<style scoped>

</style>
