<template>
    <span>
        <button @click="init"  class="btn btn-app btn-info pull-right" v-if="$root.can('registrar_orden_compra')">
            <i class="fa fa-plus"></i> Generar
        </button>
         <div class="modal fade" ref="modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-plus"></i>GENERAR ÓRDENES DE COMPRA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <model-list-select
                                            v-if="!cargando && asignaciones.length > 0"
                                            name="asignacion"
                                            v-model="id_asignacion"
                                            option-value="id"
                                            :custom-text="idAndDescripcion"
                                            :list="asignaciones"
                                            :placeholder="!cargando?'Seleccionar o buscar por folio, concepto u observaciones de solicitud':'Cargando...'"
                                            :isError="errors.has(`asignacion`)">
                                    </model-list-select>
                                    <input v-else-if="cargando" value="Cargando..." type="text" class="form-control" readonly="readonly">
                                    <input v-else-if="!cargando && asignaciones.length == 0" value="No hay asignaciones disponibles para generar órdenes de compra" type="text" class="form-control" readonly="readonly">

                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fa fa-times-circle"></i>
                                Cerrar
                            </button>
                            <button type="button" class="btn btn-primary" :disabled="id_asignacion === ''" @click="registrar">
                                <i class="fa fa-save"></i>
                                Generar
                            </button>
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
                return `Asignación: [${item.folio_asignacion_format}] Solicitud: [${item.solicitud.numero_folio_format}] - [${item.solicitud.concepto}] - [${item.solicitud.observaciones}]`
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
