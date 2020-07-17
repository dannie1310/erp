<template>
    <span>
        <button @click="init"  class="btn btn-app btn-info pull-right" v-if="$root.can('registrar_orden_compra')">
            <i class="fa fa-plus"></i> Registrar
        </button>
         <div class="modal fade" ref="modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-plus"></i>REGISTRAR ORDEN DE COMPRA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <model-list-select
                                            :disabled="cargando"
                                            name="asignacion"
                                            v-model="id_asignacion"
                                            option-value="id"
                                            :custom-text="idAndDescripcion"
                                            :list="asignaciones"
                                            :placeholder="!cargando?'Seleccionar o buscar por descripcion':'Cargando...'"
                                            :isError="errors.has(`asignacion`)">
                                    </model-list-select>

                                </div>
                            </div>
                           
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="button" class="btn btn-primary" :disabled="id_asignacion === ''" @click="registrar">Registrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import {ModelListSelect} from 'vue-search-select';
    export default {
        name: "orden-compra-create",
        components:{ModelListSelect},
        // props: ['id'],
        data() {
            return {
                asignaciones : [],
                cargando:false,
                id_asignacion:'',
            }
        },
        methods: {
            init() {
                this.cargando = true;
                return this.$store.dispatch('compras/asignacion/pendientesOrden', {
                    params: {
                        include:['solicitud'],
                        sort: 'id',
                        order: 'desc'
                    }
                }).then(data => {
                    this.asignaciones = data.data;
                    this.cargando= false;
                    $(this.$refs.modal).appendTo('body')
                    $(this.$refs.modal).modal('show');
                })
               
            },
            idAndDescripcion (item) {
                return `${item.solicitud.observaciones}`
            },
            registrar(){
                return this.$store.dispatch('compras/asignacion/generarOC', {
                    data: {
                        id: this.id_asignacion
                    }
                }).then(data => {
                    this.$emit('created', data);
                }) .finally(() => {
                    this.descargando = false;
                    $(this.$refs.modal).modal('hide');
                })
            },
        }
    }
</script>

<style scoped>

</style>
