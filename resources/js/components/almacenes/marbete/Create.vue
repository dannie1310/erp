<template>
    <span>
        <div class="row">
            <div class="col-12" v-if="$root.can('registrar_marbetes_manualmente')" :disabled="cargando">
                <button @click="init" class="btn btn-app btn-info float-right">
                    <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                    <i class="fa fa-plus" v-else></i>
                    Crear Marbete
                </button>
            </div>
        </div>

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
                                                   :disabled = "!bandera"
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

    import IndexMarbete from '../Index';
    export default {
        name: "create-marbete",
        components: {IndexMarbete},
        data() {
            return {
                almacenes:[],
                materiales:[],
                id_material: '',
                id_almacen:'',
                bandera: 0,
                cargando: false

            }
        },

        methods: {
            init() {
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
                this.getAlmacenes()
                this.getMateriales()
                this.$validator.reset()
            },
            getAlmacenes() {
                return this.$store.dispatch('cadeco/almacen/index', {
                    params: {sort: 'descripcion', order: 'asc', scope:'TipoMaterialYHerramienta' }
                })
                    .then(data => {
                        this.almacenes = data.data;
                    })

            },
            getMateriales() {
                this.cargando = true;
                return this.$store.dispatch('cadeco/material/index',{
                    id: this.id_almacen,
                    params: { sort:'descripcion', order:'asc', scope:['MaterialDescripcion', 'tipo:1,4'] }
                })
                    .then(data => {
                        this.materiales = data.data;
                        this.cargando = false;
                        this.bandera = 1;
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
                        this.$emit('created', data)

                    })
            }
        },
        computed: {

        }

    }
</script>
