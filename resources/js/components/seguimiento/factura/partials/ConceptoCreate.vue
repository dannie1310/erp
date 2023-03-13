<template>
    <span>
        <button type="button" class="btn btn-success btn-sm" @click="alta"><i class="fa fa-upload"></i>Alta de Concepto</button>
        <div class="modal fade" ref="modal_concepto" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal_concepto"> <i class="fa fa-sign-in"></i> Alta de Concepto</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group error-content">
                                        <div class="form-group">
                                            <label for="nuevo_concepto">Concepto:</label>
                                            <input class="form-control"
                                                   type="text"
                                                   style="width: 100%"
                                                   placeholder="Concepto"
                                                   name="nuevo_concepto"
                                                   id="nuevo_concepto"
                                                   data-vv-as="Nuevo Concepto"
                                                   v-validate="{required: true, max: 50 }"
                                                   v-model="nuevo_concepto"
                                                   :class="{'is-invalid': errors.has('nuevo_concepto')}">
                                            <div class="invalid-feedback" v-show="errors.has('nuevo_concepto')">{{ errors.first('nuevo_concepto') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="pull-right">
                                <button type="button" class="btn btn-secondary" v-on:click="cerrar">
                                    <i class="fa fa-close"></i>
                                    Cerrar
                                </button>
                                <button type="button" @click="validate" :disabled="nuevo_concepto == ''" class="btn btn-primary">
                                    <i class="fa fa-save"></i>
                                    Guardar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "concepto-create",
        data() {
            return {
                cargando: false,
                nuevo_concepto : ''
            }
        },
        mounted() {
            this.$validator.reset();
        },
        methods: {
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.guardarConcepto()
                        this.$validator.errors.clear();
                    }
                });
            },
            alta()
            {
                this.nuevo_concepto =  '';
                this.$validator.reset();
                $(this.$refs.modal_concepto).appendTo('body')
                $(this.$refs.modal_concepto).modal('show');
            },
            cerrar(){
                this.nuevo_concepto = '';
                $(this.$refs.modal_concepto).modal('hide');
                this.$emit('created', 'fin')
            },
            guardarConcepto() {
                return this.$store.dispatch('seguimiento/tipo-ingreso/store', {
                    tipo_ingreso: this.nuevo_concepto,
                }).then(data => {
                    this.cerrar();
                })
            }
        },
    }
</script>

<style scoped>

</style>
