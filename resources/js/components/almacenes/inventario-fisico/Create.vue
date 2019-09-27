<template>
    <span>
        <button @click="init" v-if="$root.can('iniciar_inventario_fisico')" class="btn btn-app btn-info pull-right" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i>
            Iniciar Inventario Físico
        </button>
                 <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Registrar Marbete</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <!--Almacen-->
                                <div class="col-md-12" v-if="almacenes">
                                    <div class="form-group error-content">
                                        <label for="id_almacen">Almacen</label>
                                               <select
                                                   class="form-control"
                                                   name="id_almacen"
                                                   data-vv-as="Almacen"
                                                   v-model="id_almacen"
                                                   v-validate="{required: true}"
                                                   :class="{'is-invalid': errors.has('id_almacen')}"
                                                   id="id_almacen">
                                            <option value>-- Seleccione un Almacen --</option>
                                            <option v-for="(almacen, index) in almacenes" :value="almacen.id">
                                                {{ almacen.descripcion }}
                                            </option>

                                        </select>
                                          <div class="invalid-feedback" v-show="errors.has('id_almacen')">{{ errors.first('id_almacen') }}</div>
                                    </div>
                                </div>


                                <!--Material-->
                                     <div class="col-md-12" >
                                    <div class="form-group error-content" v-if="">
                                        <label for="id_material">Material</label>
                                               <select
                                                   class="form-control"
                                                   name="id_material"
                                                   data-vv-as="Material"
                                                   v-model="id_material"
                                                   v-validate="{required: true}"
                                                   id="id_material"
                                                   :class="{'is-invalid': errors.has('id_material')}">
                                            <option value>-- Seleccione un Material --</option>
                                            <option v-for="(material, index) in materiales" :value="material.id"
                                                    data-toggle="tooltip" data-placement="left" :title="material.descripcion ">
                                                    {{ material.descripcion }}
                                            </option>

                                        </select>
                                         <div class="invalid-feedback" v-show="errors.has('id_material')">{{ errors.first('id_material') }}</div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                           <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
    </span>
</template>

<script>
    export default {
        name: "inventario-fisico-create",
        data() {
            return {
                cargando: false,
                dato:''
            }
        },
        mounted(){
        },
        methods:{
            init() {
                $(this.$refs.modal).modal('show');
                this.cargando = true;
            },
            store() {
                return this.$store.dispatch('almacenes/inventario-fisico/store', this.dato)
                    .then(data => {
                        this.$emit('created', data);
                    }).finally( ()=>{
                        this.cargando = false;
                    });
            },
        }
    }
</script>

<style>
    .error-label {
        width: 100%;
        margin-top: 0.25rem;
        font-size: 80%;
        color: #dc3545;
    }
</style>
