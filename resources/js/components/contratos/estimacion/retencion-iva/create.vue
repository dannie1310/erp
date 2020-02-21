<template>
    <span>
        <button type="button" @click="init()" class="btn btn-primary float-right" >
            Retención IVA
        </button>
        <div class="row">
            <div class="col-md-12">
                <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-md">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-plus"></i> Retención IVA</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form role="form" @submit.prevent="validate">
                                <div class="modal-body" v-if="estimacion">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group row error-content">
                                                <label for="retencion" class="col-sm-4 col-form-label">Retención de IVA: </label>
                                                <div class="col-sm-8">
                                                    <input
                                                        type="number"
                                                        step="any"
                                                        name="retencion"
                                                        data-vv-as="Retencion IVA"
                                                        v-validate="{required: true, decimal:4, min_value:0}"
                                                        class="form-control"
                                                        id="retencion"
                                                        :placeholder="estimacion.retencion_iva_format"
                                                        v-model="IVARetenido"
                                                        :class="{'is-invalid': errors.has('retencion')}">
                                                    <div class="invalid-feedback" v-show="errors.has('retencion')">{{ errors.first('retencion') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-primary">Registrar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
export default {
    name: "retencion-iva-create",
    components: {},
    props: ['id'],
    data() {
        return {
            estimacion:[],
            IVARetenido:'',
            cargando:false,
        }
    },
    mounted() {
    },
    methods: {
        init(){
            this.estimacion = [];
            this.IVARetenido = '';
            this.$validator.reset();
            this.getEstimacion();
        },
        getEstimacion(){
            this.cargando = true;
            return this.$store.dispatch('contratos/estimacion/find', {
                    id: this.id,
                }).then(data => {
                    this.estimacion = data;
                    $(this.$refs.modal).modal('show');
                }).finally(() =>{
                    this.cargando = false;
                });
        },
        update(){
            return this.$store.dispatch('contratos/estimacion/registrarRetencionIva', {
                    id: this.id,
                    params:{IVARetenido:this.IVARetenido}
                }).then(data => {
                    this.$emit('created', data);
                    $(this.$refs.modal).modal('hide');
                })
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    this.update();
                }
            });
        },
    },
}
</script>

<style>

</style>
