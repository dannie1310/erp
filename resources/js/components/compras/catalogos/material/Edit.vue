<template>
    <span>
        <button @click="find()" type="button" class="btn btn-sm btn-outline-info" :disabled="cargando">
            <i class="fa fa-pencil" v-if="!cargando"></i>
            <i class="fa fa-spinner fa-spin" v-else></i>
        </button>
        <!-- Modal -->
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">EDICIÓN DE MATERIAL</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" v-if="material" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label class="col-md-3 col-form-label">Familia: </label>
                                        <div class="col-md-9">
                                            <p class="form-control">{{ material.descripcion_familia}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="descripcion" class="col-md-3 col-form-label">Descripción:</label>
                                        <div class="col-md-9">
                                            <input
                                                type="text"
                                                name="descripcion"
                                                data-vv-as="Descripcion"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="descripcion"
                                                placeholder="Descripcion"
                                                :value="material.descripcion"
                                                @input="updateAttribute"
                                                :class="{'is-invalid': errors.has('descripcion')}">
                                            <div class="invalid-feedback" v-show="errors.has('descripcion')">{{ errors.first('descripcion') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="unidad" class="col-md-3 col-form-label">Unidad:</label>
                                        <div class="col-md-9">
                                            <select
                                                  class="form-control"
                                                  name="unidad"
                                                  id="unidad"
                                                  :value="material.unidad"
                                                  @input="updateAttribute"
                                                  data-vv-as="Unidad"
                                                  v-validate="{required: true}"
                                                  :class="{'is-invalid': errors.has('unidad')}">
                                                <option value>-- Unidad --</option>
                                                <option v-for="unidad in unidades" :value="unidad.unidad">{{ unidad.descripcion }}</option>
                                            </select>
                                            <div class="invalid-feedback" v-show="errors.has('unidad')">{{ errors.first('unidad') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import {ListSelect} from 'vue-search-select';
    export default {
        name: "material-edit",
        components:{ListSelect},
        props: ['id'],
        data() {
            return {
                cargando: false,
            }
        },
        mounted() {
            this.getUnidades();
        },
        computed: {
            material() {
                return this.$store.getters['cadeco/material/currentMaterial']
            },
            unidades() {
                return this.$store.getters['cadeco/unidad/unidades']
            },
        },
        methods: {
            find() {
                this.$store.commit('cadeco/material/SET_MATERIAL', null)
                this.cargando = true;
                return this.$store.dispatch('cadeco/material/find', {
                    id: this.id
                })
                    .then(data => {
                        this.$store.commit('cadeco/material/SET_MATERIAL', data)
                        $(this.$refs.modal).modal('show');
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            getUnidades() {
                return this.$store.dispatch('cadeco/unidad/index',{})
                    .then(data => {
                        this.$store.commit('cadeco/unidad/SET_UNIDADES', data.data);
                    })
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.update()
                    }
                });
            },
            update() {
                return this.$store.dispatch('cadeco/material/update', {
                    id: this.id,
                    data: this.material
                })
                    .then(data => {
                        this.$store.dispatch('cadeco/material/find', {
                            id: data.id,
                            params: { }
                        })
                            .then(data => {
                                $(this.$refs.modal).modal('hide');
                                this.$store.commit('cadeco/material/UPDATE_MATERIAL', data);
                            });
                    })
            },
            updateAttribute(e) {
                return this.$store.commit('cadeco/material/UPDATE_ATTRIBUTE', {attribute: $(e.target).attr('name'), value: e.target.value})
            }
        }
    }
</script>

<style scoped>

</style>
