<template>
    <span>
        <button type="button" class="btn btn-success btn-sm" @click="alta"><i class="fa fa-upload"></i>Alta de Partida</button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal"> <i class="fa fa-sign-in"></i> Alta de Partida</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group error-content">
                                        <div class="form-group">
                                            <label for="descripcion">Partida:</label>
                                            <input class="form-control"
                                                   style="width: 100%"
                                                   placeholder="Descripción"
                                                   name="partida"
                                                   id="partida"
                                                   data-vv-as="Descripción"
                                                   v-validate="{required: true}"
                                                   v-model="partida"
                                                   :class="{'is-invalid': errors.has('partida')}">
                                            <div class="invalid-feedback" v-show="errors.has('partida')">{{ errors.first('partida') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Operador:</label>
                                        <select class="form-control" name="operador" v-model="operador">
                                            <option value="+" selected>MAS</option>
                                            <option value="-">MENOS</option>
                                        </select>
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
                                <button type="button" @click="validate" :disabled="partida == ''" class="btn btn-primary">
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
    name: "descuento-create",
    data() {
        return {
            cargando: false,
            operador : '+',
            partida: ''
        }
    },
    mounted() {
        this.$validator.reset();
    },
    methods: {
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    this.guardar()
                    this.$validator.errors.clear();
                }
            });
        },
        alta()
        {
            this.partida =  '';
            this.$validator.reset();
            $(this.$refs.modal).appendTo('body')
            $(this.$refs.modal).modal('show');
        },
        cerrar(){
            this.partida = '';
            $(this.$refs.modal).modal('hide');
            this.$emit('created', 'fin')
        },
        guardar() {
            return this.$store.dispatch('seguimiento/ingreso-partida/store', {
                partida: this.partida,
                operador: this.operador
            }).then(data => {
                this.cerrar();
            })
        }
    },
}
</script>

<style scoped>

</style>
