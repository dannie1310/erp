<template>
    <span>
        <button @click="init"  class="btn btn-app pull-right" v-if="$root.can('registrar_subcontrato')">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i> Generar
        </button>
         <div class="modal fade" ref="modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-plus"></i>GENERAR SUBCONTRATO</h5>
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
                                            :placeholder="!cargando?'Seleccionar o buscar por folio, observaciones de contrato proyectado':'Cargando...'"
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
        name: "subcontrato-create",
        components:{ModelListSelect},
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
                return this.$store.dispatch('contratos/asignacion-contratista/getAsignaciones', {
                    params: {
                        scope:['pendienteSubcontrato'],
                        include:['contrato'],
                        sort: 'id_asignacion',
                        order: 'desc'
                    }
                }).then(data => {
                    this.asignaciones = data.data;
                    $(this.$refs.modal).appendTo('body')
                    $(this.$refs.modal).modal('show');
                }).finally(()=>{
                    this.cargando= false;
                });
            },
            idAndDescripcion (item) {
                return `Asignación: [${item.numero_folio_asignacion}] Contrato: [${item.contrato.numero_folio_format}] - [${item.contrato.referencia}]`
            },
            registrar(){
                return this.$store.dispatch('contratos/asignacion-contratista/generarSubcontrato', {
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
