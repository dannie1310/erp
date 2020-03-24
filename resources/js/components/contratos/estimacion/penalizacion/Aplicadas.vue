<template>
  <span>
        <button type="button" class="btn btn-primary float-right" title="Registrar" @click="getPenalizaciones()" v-if="$root.can('registrar_penalizacion_estimacion_subcontrato')">
            <i class="fa fa-plus"></i>
        </button>
        <div class="row">
            <div class="col-md-12">
                <div class="modal fade" ref="modalAplicadas" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-md">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-plus"></i> Penalización</h5>
                                <button type="button" class="close" @click="cerrar()" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form role="form" @submit.prevent="validate">
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <br>
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
                                                        v-validate="{required: true, max:1024}"
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
                                                        v-validate="{required: true, decimal:4, min_value:0.01}"
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
            id_tipo_retencion:'',
            concepto:'',
            importe:'',
            cargando:false,
        }
    },
    // mounted() {      
    // },
    methods: {
        cerrar(){
            $(this.$refs.modalAplicadas).modal('hide');
        },
        init(){
            this.id_tipo_retencion = '';
            this.concepto = '';
            this.importe = '';
            this.$validator.reset();
        },
        getPenalizaciones(){
            this.init();
            this.cargando = true;
             return this.$store.dispatch('subcontratosEstimaciones/retencion-tipo/index',{
                params:{}})
                .then(data => {
                    this.$store.commit('subcontratosEstimaciones/retencion-tipo/SET_TIPOS', data.data);
                })
                .finally(() => {
                    this.cargando = false;
                    $(this.$refs.modalAplicadas).modal('show');
                })
        },
        store(){
            this.cargando = true;
            return this.$store.dispatch('subcontratosEstimaciones/penalizacion/store', {
                id_tipo_retencion:this.id_tipo_retencion,
                id_transaccion:this.id,
                concepto:this.concepto,
                importe:this.importe,
            })
            .then(data => {
                this.$store.commit('subcontratosEstimaciones/penalizacion/INSERT_PENALIZACION', data);
                $(this.$refs.modalAplicadas).modal('hide');
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
        tipos() {
            return this.$store.getters['subcontratosEstimaciones/retencion-tipo/tipos']
        },
    }
}
</script>

<style>

</style>