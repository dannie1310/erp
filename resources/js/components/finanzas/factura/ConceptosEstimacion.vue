<template>
    <span>
        <button @click="show" type="button"  class="btn  btn-primary float-right"  title="Ver Conceptos Estimación">
            <i class="fa fa-building"></i>Conceptos para Estimación
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-building"></i> CONCEPTOS PARA ESTIMACIÓN</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th class="bg-gray-light">Concepto</th>
                                            <th class="bg-gray-light"></th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="items">
                                        <tr v-for="item in items.conceptosEstimacion">
                                            <td>{{item.descripcion_insumo}}</td>
                                            <td><input type="checkbox" id="seguir" :value="item.seleccionado" v-model="item.seleccionado"  ></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                     <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="cerrar">Cerrar</button>
                     </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
export default {
        name: "revision-conceptos-estimacion",
        props: ['items'],
        methods:{
            show() {
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show')
            },
            cerrar(){
                this.$emit('created', this.items);
            },
        },
        computed: {
        }
    }
</script>

<style>

</style>
