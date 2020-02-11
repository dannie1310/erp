<template>
    <span>
        <button @click="init" v-if="$root.can(['registrar_familia_herramienta_equipo','registrar_familia_material'])" class="btn btn-app btn-info float-right" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i>
            Registrar
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-plus"></i> REGISTRAR FAMILIA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="tipo" class="col-sm-2 col-form-label">Tipo: </label>
                                        <div class="col-sm-10">
                                            <select
                                                type="text"
                                                name="tipo"
                                                data-vv-as="Tipo"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="tipo"
                                                v-model="tipo"
                                                :class="{'is-invalid': errors.has('tipo')}"
                                            >
                                                    <option value>-- Seleccione un tipo --</option>
                                                    <option v-for="tipo in tipos" :value="tipo.id">{{ tipo.descripcion }}</option>
                                            </select>
                                            <div class="invalid-feedback" v-show="errors.has('tipo')">{{ errors.first('tipo') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="descripcion" class="col-sm-2 col-form-label">Descripción:</label>
                                        <div class="col-sm-10">
                                            <input
                                                type="text"
                                                name="descripcion"
                                                data-vv-as="Descripcion"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="descripcion"
                                                placeholder="Descripcion"
                                                v-model="descripcion"
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
        name: "familia-create",
        data() {
                return {
                    cargando:false,
                    tipos: [
                        {id: 1, descripcion: 'Materiales'},
                        {id: 4, descripcion: 'Herramienta y Equipo'}
                    ],
                    tipo:'',
                    descripcion:''
                }
        },
        methods: {
            init() {
                  this.cargando = false;
                $(this.$refs.modal).modal('show');
            },
            store() {
                return this.$store.dispatch('cadeco/familia/store', this.$data)
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
