<template>
<span>
        <button type="button" @click="init()" v-if="$root.can('aplicar_retencion_isr_estimacion')" class="btn btn-primary float-right" title="Retención ISR">
            Retención ISR
        </button>
        <div class="row">
            <div class="col-md-12">
                <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-md">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-plus"></i> Retenciones ISR</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form role="form" @submit.prevent="validate">
                                <div class="modal-body" v-if="estimacion">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group row error-content">
                                                <label for="retencion125" class="col-sm-4 col-form-label">Retención de ISR 1.25%: </label>
                                                <div class="col-sm-8">
                                                    <input
                                                        type="number"
                                                        step="any"
                                                        name="retencion125"
                                                        data-vv-as="Retencion ISR 1.25%"
                                                        v-validate="{decimal:4, min_value:0}"
                                                        class="form-control"
                                                        id="retencion125"
                                                        :placeholder="estimacion.porcentaje_isr_retenido==1.25?estimacion.monto_isr_retenido_format:'$ 0.00'"
                                                        v-model="ISRRetenido125"
                                                        :class="{'is-invalid': errors.has('retencion125')}">
                                                    <div class="invalid-feedback" v-show="errors.has('retencion125')">{{ errors.first('retencion125') }}</div>
                                                </div>
                                            </div>
                                            <div class="form-group row error-content">
                                                <label for="retencion10" class="col-sm-4 col-form-label">Retención de ISR 10%: </label>
                                                <div class="col-sm-8">
                                                    <input
                                                        type="number"
                                                        step="any"
                                                        name="retencion10"
                                                        data-vv-as="Retencion ISR 10%"
                                                        v-validate="{decimal:4, min_value:0}"
                                                        class="form-control"
                                                        id="retencion10"
                                                        :placeholder="estimacion.porcentaje_isr_retenido==10?estimacion.monto_isr_retenido_format:'$ 0.00'"
                                                        v-model="ISRRetenido10"
                                                        :class="{'is-invalid': errors.has('retencion10')}">
                                                    <div class="invalid-feedback" v-show="errors.has('retencion10')">{{ errors.first('retencion10') }}</div>
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
    name: "retencion-isr-create",
    components: {},
    props: ['id'],
    data() {
        return {
            estimacion:[],
            ISRRetenido125:'',
            ISRRetenido10:'',
            cargando:false,
        }
    },
    mounted() {
    },
    methods: {
        init(){
            this.estimacion = [];
            this.ISRRetenido125 = '';
            this.ISRRetenido10 = '';
            this.$validator.reset();
            this.getEstimacion();
        },
        getEstimacion(){
            this.cargando = true;
            return this.$store.dispatch('contratos/estimacion/find', {
                    id: this.id,
                }).then(data => {
                    this.estimacion = data;
                $(this.$refs.modal).appendTo('body')
                    $(this.$refs.modal).modal('show');
                }).finally(() =>{
                    this.cargando = false;
                });
        },
        update(){
            return this.$store.dispatch('contratos/estimacion/registrarRetencionISR', {
                    id: this.id,
                    params:{retencionISR125:this.ISRRetenido125, retencionISR10:this.ISRRetenido10}
                }).then(data => {
                    $(this.$refs.modal).modal('hide');
                })
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    if(this.ISRRetenido125 == '' && this.ISRRetenido10 == ''){
                        swal('¡Error!', 'Ingrese al menos una retención.', 'error')
                    }else{
                        this.update();
                    }
                    
                }
            });
        },
    },
}
</script>

<style>

</style>