<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-info" title="Editar">
            <i class="fa fa-pencil"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-plus"></i> EDITAR ORIGEN</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div v-if="!origen">
                                <div class="row" >
                                    <div class="col-md-12">
                                        <div class="spinner-border text-success" role="status">
                                            <span class="sr-only">Cargando...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else>
                                <div class="row">
                                    <div class="col-md-6"></div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="form-group col-md-12 error-content">
                                                <div class="col-md-2">
                                                    <label >Tipo:</label>
                                                </div>
                                                <div class="col-md-10">
                                                    <div class="btn-group btn-group-toggle">
                                                        <label class="btn btn-outline-secondary" :class="tipo === Number(key) ? 'active': ''" v-for="(tip, key) in tipos" :key="key">
                                                        <input type="radio"
                                                               class="btn-group-toggle"
                                                               name="tipo"
                                                               :id="'tipo' + key"
                                                               :value="key"
                                                               v-validate="{required: true}"
                                                               :error="errors.has('tipo')"
                                                               autocomplete="off"
                                                               v-model.number="tipo">
                                                        {{ tip }}
                                                        </label>
                                                    </div>
                                                    <div style="display:block" class="invalid-feedback" v-show="errors.has('tipo')">{{ errors.first('tipo') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="form-group col-md-12 error-content">
                                                <div class="form-group">
                                                    <label>Tipo de Origen:</label>
                                                    <input disabled="true"
                                                           type="text"
                                                           name="tipo"
                                                           class="form-control"
                                                           id="tipo"
                                                           v-model="origen.tipo" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-12 error-content">
                                                <label for="descripcion" class="col-form-label">Descripción:</label>
                                                <input style="text-transform:uppercase;"
                                                       disabled="true"
                                                       type="text"
                                                       name="descripcion"
                                                       class="form-control"
                                                       id="descripcion"
                                                       v-model="origen.descripcion" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0" @click="validate"><i class="fa fa-save"></i> Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "origen-edit",
        props: ['id'],
        data() {
            return {
                tipo : '',
                tipos: {
                    1: "Interno",
                    0: "Externo"
                },
            }
        },
        methods: {
            salir() {
                $(this.$refs.modal).modal('hide');
            },
            find() {
                this.tipo= '';
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
                this.$store.commit('acarreos/origen/SET_ORIGEN', null);
                return this.$store.dispatch('acarreos/origen/find', {
                    id: this.id,
                    params: {}
                }).then(data => {
                    this.tipo = data.id_tipo
                    this.$store.commit('acarreos/origen/SET_ORIGEN', data);
                    this.carga = true;
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
                var datos = {
                    'interno' : this.tipo,
                }
                return this.$store.dispatch('acarreos/origen/update', {
                    id: this.id,
                    data: datos
                })
                .then((data) => {
                    this.$store.commit('acarreos/origen/UPDATE_ORIGEN', data);
                    this.salir()
                })
            },
        },
        computed: {
            origen() {
                return this.$store.getters['acarreos/origen/currentOrigen']
            }
        }
    }
</script>

<style scoped>

</style>
