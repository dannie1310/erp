<template>
  <span>
        <button type="button" @click="itemsContratista()" class="btn btn-primary float-right espacio" v-if="$root.can('registrar_descuento_estimacion_subcontrato')" title="Editar">
            <i class="fa fa-plus"></i> Agregar
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
                                                <model-list-select
                                                            :disabled="cargando"
                                                            name="id_material"
                                                            v-model="id_material"
                                                            option-value="id_material"
                                                            option-text="descripcion"
                                                            :list="items"
                                                            :placeholder="!cargando?'Seleccionar o buscar material por descripción':'Cargando...'"
                                                            :isError="errors.has(`id_material`)">
                                                    </model-list-select>
                                                    <div class="invalid-feedback" v-show="errors.has('id_material')">{{ errors.first('id_material') }}</div>

                                            </div>
                                            <div class="col-2">
                                                <input
                                                        type="number"
                                                        step="any"
                                                        name="cantidad"
                                                        data-vv-as="Cantidad"
                                                        v-validate="{required: true, min_value:0.01,max_value:cantidad_maxima, decimal:5}"
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
                                    <button type="submit" class="btn btn-primary"  :disabled="items.length == 0"><i class="fa fa-save"></i>  Registrar</button>
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
import {ModelListSelect} from 'vue-search-select';
export default {
    name: "deductiva-create",
    components: {ModelListSelect},
    props: ['id', 'id_empresa'],
    data() {
        return {
            items:[],
            id_material:'',
            cantidad:'',
            cantidad_maxima:'',
            precio_unitario:'',
            scope: ['insumos', 'suministrables'],
            cargando:false,
        }
    },
    mounted() {
    },
    methods: {
        cerrar(){
            this.resetVals();
            $(this.$refs.modalAgregar).modal('hide');
        },
        store() {
            return this.$store.dispatch('subcontratosEstimaciones/descuento/store',  {
                id_transaccion:this.id,
                id_material:this.id_material,
                cantidad:this.cantidad,
                precio:this.precio_unitario
            })
            .then((data) => {
                this.cerrar();
            })
        },
        itemsContratista(){
            this.items = [];
            return this.$store.dispatch('subcontratosEstimaciones/descuento/listItems', {
                id: this.id_empresa,
                params: {include: 'material', id_estimacion:this.id}
            }).then(data => {
                if(data.length === 0){
                    swal('Atención', 'No hay material disponible para agregar como deductiva.', 'warning');
                }else{
                    this.items = data;
                    $(this.$refs.modalAgregar).modal('show');
                }
            })
            .finally(()=>{
                this.cargando = false;
            })
        },
        resetVals(){
            this.items = [];
            this.id_material = '';
            this.cantidad = '';
            this.cantidad_maxima = '';
            this.precio_unitario = '';
            this.$validator.reset();
        },
        setValores(){
            this.items.map(item => {
                if (this.id_material === item.id_material) {
                    this.cantidad = item.cantidad_disponible;
                    this.cantidad_maxima = item.cantidad_disponible;
                    this.precio_unitario = item.precio;
                }
            });
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    this.store();
                }
            });
        },
    },
    watch: {
        id_material(value){
            if(value != ''){
                this.setValores();
            }
        },
    }

}
</script>

<style>
.espacio {
  margin-bottom: 25px;
}

</style>
