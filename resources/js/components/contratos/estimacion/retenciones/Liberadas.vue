<template>
  <span>
        <button type="button" class="btn btn-primary float-right" title="Registrar" @click="init()" v-if="$root.can('registrar_liberacion_estimacion_subcontrato')" >
            <i class="fa fa-plus"></i>
        </button>
        <div class="row">
            <div class="col-md-12">
                <div class="modal fade" ref="modalLiberadas" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-md">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-plus"></i> Liberación</h5>
                                <button type="button" class="close" @click="cerrar()" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form role="form" @submit.prevent="validate">
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="concepto" class="col-form-label float-right"><h3><b>POR LIBERAR</b></h3> </label>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group row error-content">
                                                <label for="concepto" class="col-sm-3 col-form-label">Concepto: </label>
                                                <div class="col-sm-9">
                                                    <textarea
                                                        name="concepto"
                                                        id="concepto"
                                                        class="form-control"
                                                        v-model="concepto"
                                                        v-validate="{required: true}"
                                                        data-vv-as="Concepto"
                                                        :class="{'is-invalid': errors.has('concepto')}"
                                                    ></textarea>
                                                    <div class="invalid-feedback" v-show="errors.has('concepto')">{{ errors.first('concepto') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group row error-content">
                                                <label for="importe" class="col-sm-3 col-form-label">Importe: </label>
                                                <div class="col-sm-9">
                                                    <input
                                                        type="number"
                                                        step="any"
                                                        name="importe"
                                                        data-vv-as="Importe"
                                                        v-validate="{required: true, numeric:true}"
                                                        class="form-control"
                                                        id="importe"
                                                        placeholder="Importe"
                                                        v-model="importe"
                                                        :class="{'is-invalid': errors.has('importe')}">
                                                    <div class="invalid-feedback" v-show="errors.has('importe')">{{ errors.first('importe') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" @click="cerrar()">Cerrar</button>
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
    name: "aplicadas-create",
    components: {},
    props: ['id'],
    data() {
        return {
            concepto:'',
            importe:'',
            cargando:false,
        }
    },
    mounted() {      
    },
    methods: {
        cerrar(){
            $(this.$refs.modalLiberadas).modal('hide');
        },
        init(){
            this.concepto = '';
            this.importe = '';
            this.$validator.reset();
            $(this.$refs.modalLiberadas).modal('show');
        },
        store(){
            this.cargando = true;
            return this.$store.dispatch('subcontratosEstimaciones/retencion-liberacion/store', {
                id_transaccion:this.id,
                concepto:this.concepto,
                importe:this.importe,
            })
            .then(data => {
                this.$store.commit('subcontratosEstimaciones/retencion-liberacion/INSERT_LIBERACION', data);
                $(this.$refs.modalLiberadas).modal('hide');
            }).finally( ()=>{
                this.cargando = false;
            });
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    this.store();
                }
            });
        },
    },
    computed: {
    }
}
</script>

<style>

</style>