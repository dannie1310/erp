<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_solicitud_pago_anticipado')" class="btn btn-app btn-info pull-right" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i>
            Registrar Material
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> REGISTRAR MATERIAL</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="tipo" class="col-sm-2 col-form-label">Material: </label>
                                        <div class="col-sm-10">
                                            <FamiliaSelect
                                                    name="tipo"
                                                    id="tipo"
                                                    data-vv-as="Material"
                                                    v-validate="{required: true}"
                                                    v-model="dato.tipo"
                                                    :class="{'is-invalid': errors.has('tipo')}">

                                            </FamiliaSelect>
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
                                                :disabled="!dato.tipo"
                                                type="text"
                                                name="descripcion"
                                                data-vv-as="Descripcion"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="descripcion"
                                                placeholder="Descripcion"
                                                v-model="dato.descripcion"
                                                :class="{'is-invalid': errors.has('descripcion')}">
                                            <div class="invalid-feedback" v-show="errors.has('descripcion')">{{ errors.first('descripcion') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
<!--                             <div class="row">-->
<!--                                <div class="col-md-12">-->
<!--                                    <div class="form-group row error-content">-->
<!--                                        <label for="unidad" class="col-sm-2 col-form-label">Unidad: </label>-->
<!--                                        <div class="col-sm-3">-->
<!--                                            <select-->
<!--                                                type="text"-->
<!--                                                name="unidad"-->
<!--                                                data-vv-as="Unidad"-->
<!--                                                v-validate="{required: true}"-->
<!--                                                class="form-control"-->
<!--                                                id="unidad"-->
<!--                                                v-model="unidad"-->
<!--                                                :class="{'is-invalid': errors.has('unidad')}"-->
<!--                                            >-->
<!--                                                    <option value>&#45;&#45; Seleccione un Unidad &#45;&#45;</option>-->
<!--                                                    <option v-for="unidad in unidades" :value="unidad.id">{{ unidad.descripcion }}</option>-->
<!--                                            </select>-->
<!--                                            <div class="invalid-feedback" v-show="errors.has('tipo')">{{ errors.first('tipo') }}</div>-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
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
    import FamiliaSelect from "../familia/Select";
    export default {
        name: "material-create",
        components: {FamiliaSelect},
        data() {
                return {
                    cargando:false,
                    dato: {
                        tipo: '',
                        descripcion: ''

                    }
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
