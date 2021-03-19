<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_tiro')" class="btn btn-app btn-info pull-right">
            <i class="fa fa-plus"></i> Registrar
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">REGISTRAR ORIGEN</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div v-if="!tipos_origenes">
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
                                                    <label for="tipo_origen">Tipo de Origen:</label>
                                                    <select class="form-control"
                                                            name="tipo_origen"
                                                            data-vv-as="Tipo de Origen"
                                                            v-model="tipo_origen"
                                                            v-validate="{required: true}"
                                                            :error="errors.has('tipo_origen')"
                                                            :class="{'is-invalid': errors.has('tipo_origen')}"
                                                            id="tipo_origen">
                                                        <option value>-- Seleccionar--</option>
                                                        <option v-for="tipo in tipos_origenes" :value="tipo.id" >{{ tipo.descripcion}}</option>
                                                    </select>
                                                    <div style="display:block" class="invalid-feedback" v-show="errors.has('tipo_origen')">{{ errors.first('tipo_origen') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-12 error-content">
                                                <label for="descripcion" class="col-form-label">Descripción:</label>
                                                <input style="text-transform:uppercase;"
                                                        type="text"
                                                        name="descripcion"
                                                        data-vv-as="'Descripción'"
                                                        v-validate="{required: true, min:6}"
                                                        class="form-control"
                                                        id="descripcion"
                                                        v-model="descripcion"
                                                        :class="{'is-invalid': errors.has('descripcion')}" />
                                                <div class="invalid-feedback" v-show="errors.has('descripcion')">{{ errors.first('descripcion') }}</div>
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
        name: "Create",
        data() {
            return {
                tipo_origen : '',
                descripcion : '',
                tipo : '',
                tipos_origenes : [],
                tipos: {
                    1: "Interno",
                    0: "Externo"
                },
            }
        },
        mounted() {
            this.$validator.reset();
            this.getTiposOrigen()
        },
        methods : {
            init() {
                this.descripcion = '';
                this.tipo_origen = '';
                this.tipo = '';
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            },
            validate() {
                this.$validator.validate().then(result => {
                    this.descripcion = this.descripcion.toUpperCase();
                    if (result) {
                        this.store();
                    }
                });
            },
            store() {
                return this.$store.dispatch('acarreos/origen/store', {
                    Descripcion: this.descripcion,
                    IdTipoOrigen: this.tipo_origen,
                    interno: this.tipo
                })
                    .then((data) => {
                        $(this.$refs.modal).modal('hide');
                        this.$emit('created', data)
                    });
            },
            getTiposOrigen() {
                return this.$store.dispatch('acarreos/tipo-origen/index', {
                    params: { scope: 'activo', sort: 'IdTipoOrigen', order: 'asc',}
                }).then(data => {
                    this.tipos_origenes = data.data
                });
            },
        }
    }
</script>

<style scoped>

</style>
