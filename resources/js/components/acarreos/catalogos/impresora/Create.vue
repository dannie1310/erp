<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_impresora')" class="btn btn-app pull-right">
            <i class="fa fa-plus"></i> Registrar
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-plus"></i> REGISTRAR IMPRESORA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="form-group col-md-12 error-content">
                                            <label for="mac" class="col-form-label">MAC:</label>
                                             <input
                                                 type="text"
                                                 name="mac"
                                                 data-vv-as="MAC"
                                                 v-validate="{required: true, regex: '^([0-9A-z]{2}:){5}([0-9A-z]{2})$'}"
                                                 class="form-control"
                                                 v-mask="{regex: '^([0-9A-z]{2}:){5}([0-9A-z]{2})$'}"
                                                 id="mac"
                                                 placeholder="MAC"
                                                 v-model="mac"
                                                 :class="{'is-invalid': errors.has('mac')}">
                                            <div class="invalid-feedback" v-show="errors.has('mac')">{{ errors.first('mac') }}</div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-12 error-content">
                                            <label for="marca" class="col-form-label">Marca:</label>
                                            <input  type="text"
                                                    name="marca"
                                                    data-vv-as="'Marca'"
                                                    v-validate="{required: true}"
                                                    class="form-control"
                                                    placeholder="Marca"
                                                    id="marca"
                                                    v-model="marca"
                                                    :class="{'is-invalid': errors.has('marca')}" />
                                            <div class="invalid-feedback" v-show="errors.has('marca')">{{ errors.first('marca') }}</div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-12 error-content">
                                            <label for="modelo" class="col-form-label">Modelo:</label>
                                            <input  type="text"
                                                    name="modelo"
                                                    data-vv-as="'Modelo'"
                                                    v-validate="{required: true}"
                                                    class="form-control"
                                                    placeholder="Modelo"
                                                    id="modelo"
                                                    v-model="modelo"
                                                    :class="{'is-invalid': errors.has('modelo')}" />
                                            <div class="invalid-feedback" v-show="errors.has('modelo')">{{ errors.first('modelo') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
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
                mac : '',
                marca : '',
                modelo : ''
            }
        },
        mounted() {
            this.$validator.reset();
        },
        methods : {
            init() {
                this.mac = '';
                this.marca = '';
                this.modelo = '';
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        var mac = this.mac;
                        mac = mac.replace(/_/g, "");
                        if(mac.length < 17)
                        {
                            swal('¡Error!', 'La MAC Address debe tener 12 digitos', 'error')
                        }
                        else {
                            this.store()
                        }
                    }
                });
            },
            store() {
                return this.$store.dispatch('acarreos/impresora/store', {
                    mac: this.mac,
                    marca: this.marca,
                    modelo: this.modelo
                })
                    .then((data) => {
                        $(this.$refs.modal).modal('hide');
                        this.$emit('created', data)
                    });
            },
        }
    }
</script>

<style scoped>

</style>
