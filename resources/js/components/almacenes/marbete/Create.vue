<template>
    <span>
        <button @click="init" v-if="" class="btn btn-app btn-info pull-right" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i>
            Crear Marbete
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

    import Index from '../Index';
    export default {
        name: "create-marbete",
        // props: ['id'],
        components: {Index},
        data() {
            return {
                almacenes:[],
                materiales:[],
                id_material: '',
                id_almacen:'',
                aux:'',
                id_inventario_fisico:'',
                id:'',

            }
        },

        mounted() {
            this.getAlmacenes()
            this.getMateriales()
            this.id = 3

        },

        methods: {
            init() {

                this.id_inventario_fisico = this.id;
                $(this.$refs.modal).modal('show');
                this.$validator.reset()
            },
            getAlmacenes() {
                return this.$store.dispatch('cadeco/almacen/index', {
                    params: {sort: 'descripcion', order: 'asc', scope:'AlmacenInventario' }
                })
                    .then(data => {
                        this.almacenes = data.data;
                    })

            },
            getMateriales() {
                this.aux=1;
                console.log(this.id_almacen);

                return this.$store.dispatch('cadeco/material/index',{
                    id: this.id_almacen,
                    params: { sort:'descripcion', order:'asc', scope:'MaterialInventario' }
                })
                    .then(data => {
                        this.materiales = data.data;
                    })

            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store()
                    }
                });
            },
            store() {
                return this.$store.dispatch('almacenes/marbete/store', this.$data)
                    .then((data) => {
                        $(this.$refs.modal).modal('hide');
                        this.$emit('created',data)
                    })
            }
        },

        computed: {


        }
    }
</script>
