<template>
  <span>
        <button type="button" @click="init()" class="btn btn-primary float-right espacio" title="Editar">
            Agregar
        </button>
        <div class="row">
            <div class="col-md-12">
                <div class="modal fade" ref="modalAgregar" data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-plus"></i> Deductivas</h5>
                                <button type="button" class="close" @click="cerrar()" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form role="form" @submit.prevent="validate">
                                <div class="modal-body">
                                    <div class="invoice p-3 mb-3">
                                        <div class="row">   
                                        <!-- <div class="col-12"> -->
                                            <div class="col-8" >
                                                <MaterialSelect
                                                    :scope="scope"
                                                    :name="material"
                                                    v-model="material"
                                                    data-vv-as="Material"
                                                    v-validate="{required: true}"
                                                    ref="MaterialSelect"
                                                    :disableBranchNodes="false"/>
                                                    
                                            </div>
                                            <div class="col-2">
                                                <input
                                                        type="number"
                                                        step="any"
                                                        name="cantidad"
                                                        data-vv-as="Cantidad"
                                                        v-validate="{required: true, min_value:0.01, decimal:4}"
                                                        class="form-control"
                                                        id="cantidad"
                                                        placeholder="Cantidad"
                                                        v-model="cantidad"
                                                        :class="{'is-invalid': errors.has('cantidad')}">
                                                <div class="invalid-feedback" v-show="errors.has('cantidad')">{{ errors.first('cantidad') }}</div>
                                            </div>
                                            <div class="col-2">
                                                <input
                                                        type="number"
                                                        step="any"
                                                        name="precio_unitario"
                                                        data-vv-as="Precio Unitario"
                                                        v-validate="{required: true, min_value:0.01, decimal:4}"
                                                        class="form-control"
                                                        id="precio_unitario"
                                                        placeholder="Precio Unitario"
                                                        v-model="precio_unitario"
                                                        :class="{'is-invalid': errors.has('precio_unitario')}">
                                                <div class="invalid-feedback" v-show="errors.has('precio_unitario')">{{ errors.first('precio_unitario') }}</div>
                                            </div>
                                        <!-- </div> -->
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" @click="cerrar()">Cerrar</button>
                                    <button type="submit" class="btn btn-primary"  :disabled="material.length == 0"><i class="fa fa-plus"></i>  Registrar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
  </span>
</template>

<script>
import MaterialSelect from '../../../cadeco/material/SelectAutocomplete'
export default {
    name: "deductiva-create",
    components: {MaterialSelect},
    props: ['id'],
    data() {
        return {
            material:[],
            cantidad:'',
            precio_unitario:'',
            scope: ['insumos', 'suministrables'],
        }
    },
    mounted() {
    },
    methods: {
        cerrar(){
            this.material = [];
            this.cantidad = '';
            this.precio_unitario = '';
            this.$validator.reset();
            $(this.$refs.modalAgregar).modal('hide');
        },
        init(){
            this.material = [];
            this.cantidad = '';
            this.precio_unitario = '';
            this.$validator.reset();
            $(this.$refs.modalAgregar).modal('show');
        },
        store() {
            return this.$store.dispatch('subcontratosEstimaciones/descuento/store',  {
                id_transaccion:this.id,
                id_material:this.material.id,
                cantidad:this.cantidad,
                precio:this.precio_unitario
            })
            .then((data) => {
                this.$emit('created',data);
                this.cerrar();
            })
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    this.store();
                }
            });
        },
    }

}
</script>

<style>
.espacio {
  margin-bottom: 25px;
}

</style>