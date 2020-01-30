<template>
  <span>
        <button type="button" @click="init()" class="btn btn-primary" title="Editar">
            Deductivas
        </button>
        <div class="row">
            <div class="col-md-12">
                <div class="modal fade" ref="modal" data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-pencil"></i> Deductivas</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="invoice p-3 mb-3">
                                    <div class="row">   
                                    <!-- <div class="col-12"> -->
                                        <div class="col-10" >
                                            <MaterialSelect
                                                :scope="scope"
                                                :name="material"
                                                v-model="material"
                                                data-vv-as="Material"
                                                v-validate="{required: true}"
                                                ref="MaterialSelect"
                                                :disableBranchNodes="false"/>
                                                
                                        </div>
                                        <div class="col-2" v-if="$root.can('registrar_material_proveedor')">
                                            <button type="submit" class="btn btn-primary"  :disabled="material.length == 0"><i class="fa fa-plus"></i>  Registrar</button>
                                        </div>
                                    <!-- </div> -->
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-primary">Actualizar</button>
                            </div>
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
    name: "deductivas-estimacion",
    components: {MaterialSelect},
        props: ['id'],
        data() {
            return {
                material:[],
                scope: ['insumos', 'suministrables'],
            }
        },
        mounted() {
        },
        methods: {
            init(){
                console.log(this.id);
                $(this.$refs.modal).modal('show');
            }
        }

}
</script>

<style>

</style>