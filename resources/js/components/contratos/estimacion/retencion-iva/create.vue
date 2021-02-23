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
                                <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-plus"></i> Retenciones IVA</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form role="form" @submit.prevent="validate">
                                <div class="modal-body" v-if="estimacion">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group row error-content">
                                                <label for="retencion4" class="col-sm-4 col-form-label">Retención de IVA 4%: </label>
                                                <div class="col-sm-8">
                                                    <input
                                                        type="number"
                                                        step="any"
                                                        name="retencion4"
                                                        data-vv-as="Retencion IVA 4%"
                                                        v-validate="{decimal:4, min_value:0}"
                                                        class="form-control"
                                                        id="retencion4"
                                                        :placeholder="estimacion.retencion_iva4_format"
                                                        v-model="IVARetenido4"
                                                        :class="{'is-invalid': errors.has('retencion4')}">
                                                    <div class="invalid-feedback" v-show="errors.has('retencion4')">{{ errors.first('retencion4') }}</div>
                                                </div>
                                            </div>
                                            <div class="form-group row error-content">
                                                <label for="retencion6" class="col-sm-4 col-form-label">Retención de IVA 6%: </label>
                                                <div class="col-sm-8">
                                                    <input
                                                        type="number"
                                                        step="any"
                                                        name="retencion6"
                                                        data-vv-as="Retencion IVA 6%"
                                                        v-validate="{decimal:4, min_value:0}"
                                                        class="form-control"
                                                        id="retencion6"
                                                        :placeholder="estimacion.retencion_iva6_format"
                                                        v-model="IVARetenido6"
                                                        :class="{'is-invalid': errors.has('retencion6')}">
                                                    <div class="invalid-feedback" v-show="errors.has('retencion6')">{{ errors.first('retencion6') }}</div>
                                                </div>
                                            </div>
                                            <div class="form-group row error-content">
                                                <label for="retencion" class="col-sm-4 col-form-label">Retención de IVA 2/3: </label>
                                                <div class="col-sm-8">
                                                    <input
                                                        type="number"
                                                        step="any"
                                                        name="retencion"
                                                        data-vv-as="Retencion IVA 2/3"
                                                        v-validate="{decimal:4, min_value:0}"
                                                        class="form-control"
                                                        id="retencion"
                                                        :placeholder="estimacion.retencion_iva23_format"
                                                        v-model="retencion_2_3"
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
            IVARetenido4:'',
            IVARetenido6:'',
            retencion_2_3:'',
            cargando:false,
        }
    },
    mounted() {
    },
    methods: {
        init(){
            this.estimacion = [];
            this.IVARetenido4 = '';
            this.IVARetenido6 = '';
            this.retencion_2_3 = '';
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
            return this.$store.dispatch('contratos/estimacion/registrarRetencionIva', {
                    id: this.id,
                    params:{retencion4:this.IVARetenido4, retencion6:this.IVARetenido6, retencionIVA_2_3:this.retencion_2_3}
                }).then(data => {
                    $(this.$refs.modal).modal('hide');
                })
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    if(this.IVARetenido4 == '' && this.IVARetenido6 == '' && this.retencion_2_3 == ''){
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
