<template>
    <span>
        <button class="btn btn-primary" title="Agregar concepto extraordinario" @click="init()">
            <i class="fa fa-plus-circle"></i> Agregar Concepto Extraordinario
        </button>

                <div class="modal fade" ref="modalExtraordinario" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-plus"></i> Concepto Extraordinario</h5>
                                <button type="button" class="close" @click="cerrar()" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form role="form" @submit.prevent="validate">
                                <div class="modal-body">
                                    <div class="form-group row error-content">
                                        <label for="clave" class="col-md-2 col-form-label">Clave:</label>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" id="clave" placeholder="Clave"
                                                   name="clave"
                                                   data-vv-as="Clave"
                                                   v-validate="{required: true}"
                                                   :class="{'is-invalid': errors.has('clave')}"
                                            v-model="concepto.clave">
                                            <div class="invalid-feedback" v-show="errors.has('clave')">{{ errors.first('clave') }}</div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="descripcion" class="col-md-2 col-form-label">Descripción:</label>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" id="descripcion" placeholder="Descripción"
                                                   v-model="concepto.descripcion"
                                                   name="descripcion"
                                                   data-vv-as="Descripción"
                                                   v-validate="{required: true}"
                                                   :class="{'is-invalid': errors.has('descripcion')}"
                                            >
                                            <div class="invalid-feedback" v-show="errors.has('descripcion')">{{ errors.first('descripcion') }}</div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="cantidad" class="col-md-2 col-form-label">Cantidad:</label>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" id="cantidad" placeholder="Cantidad" v-model="concepto.cantidad"
                                                   name="cantidad"
                                                   v-validate="{required:true, decimal:4}"
                                                   :class="{'is-invalid': errors.has('cantidad')}">
                                            <div class="invalid-feedback" v-show="errors.has('cantidad')">{{ errors.first('cantidad') }}</div>
                                        </div>
                                        <label for="unidad" class="col-md-2 col-form-label">Unidad:</label>
                                        <div class="col-md-4">
                                            <select
                                                type="text"
                                                name="unidad"
                                                data-vv-as="Unidad"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="unidad"
                                                v-model="concepto.unidad"
                                                :class="{'is-invalid': errors.has('unidad')}">
                                                <option value>--Unidad--</option>
                                                <option v-for="unidad in unidades" :value="unidad.unidad">{{ unidad.descripcion }}</option>
                                            </select>
                                            <div class="invalid-feedback" v-show="errors.has('unidad')">{{ errors.first('unidad') }}</div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="precio" class="col-md-2 col-form-label">Precio:</label>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" id="precio" placeholder="Precio"
                                                   v-model="concepto.precio"
                                                   name="precio"
                                                   data-vv-as="Precio"
                                                   v-validate="{required: true, decimal:4, min:0.01}"
                                                   :class="{'is-invalid': errors.has('precio')}"
                                            >
                                            <div class="invalid-feedback" v-show="errors.has('precio')">{{ errors.first('precio') }}</div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="destino" class="col-md-2 col-form-label">Destino:</label>
                                        <div class="col-md-10">
                                            <concepto-select
                                                name="destino"
                                                v-validate="{required: true}"
                                                data-vv-as="Concepto"
                                                id="destino"
                                                v-model="concepto.destino"
                                                :error="errors.has('destino')"
                                                ref="conceptoSelect"
                                                :disableBranchNodes="true"
                                            ></concepto-select>
                                            <div class="error-label" v-show="errors.has('destino')">{{ errors.first('destino') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" @click="cerrar()">
                                        <i class="fa fa-close"  ></i>
                                        Cerrar
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-plus-circle"></i>
                                        Agregar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
    </span>

</template>

<script>
import  ConceptoSelect from "../../../cadeco/concepto/Select";
export default {
name: "CreateConceptoExtaordinario",
    props: ['concepto'],
    data(){
        return {
            unidades:'',
        }
    },
    components: {ConceptoSelect},
    mounted() {
        this.getUnidades();
    },
    methods:{
        init() {
            $(this.$refs.modalExtraordinario).modal('show');
            this.$validator.reset();
        },
        cerrar() {
            $(this.$refs.modalExtraordinario).modal('hide');
            this.$validator.reset();
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    this.$emit("agrega-extraordinario",this.concepto);
                    $(this.$refs.modalExtraordinario).modal('hide');
                    this.$validator.reset()
                }
            });
        },
        getUnidades() {
            return this.$store.dispatch('cadeco/unidad/index', {
                params: {sort: 'unidad',  order: 'asc'}
            })
                .then(data => {
                    this.unidades= data.data;
                })
        },
    }
}
</script>

<style scoped>

</style>
