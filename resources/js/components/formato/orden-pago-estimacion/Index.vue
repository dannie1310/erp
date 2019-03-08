<template>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form role="form" @submit.prevent="validate">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Datos de Consulta</h3>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group error-content">
                                    <label for="id_empresa">Contratista</label>
                                    <select
                                            type="text"
                                            name="id_empresa"
                                            data-vv-as="Empresa"
                                            v-validate="{required: true}"
                                            class="form-control"
                                            id="id_empresa"
                                            v-model="id_empresa"
                                            :class="{'is-invalid': errors.has('id_empresa')}"
                                    >
                                        <option value>-- Seleccione --</option>
                                        <option v-for="c in empresas" :value="c.id">{{ c.razon_social }}</option>
                                    </select>
                                    <div class="invalid-feedback" v-show="errors.has('id_empresa')">{{ errors.first('id_empresa') }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group error-content">
                                    <label for="id_subcontrato">Subcontrato</label>
                                    <select
                                            type="text"
                                            name="id_subcontrato"
                                            data-vv-as="Subcontrato"
                                            v-validate="{required: true}"
                                            class="form-control"
                                            id="id_subcontrato"
                                            v-model="id_subcontrato"
                                            :class="{'is-invalid': errors.has('id_subcontrato')}"
                                    >
                                        <option value>-- Seleccione --</option>
                                        <option v-for="c in subcontratos" :value="c.id">{{ c.referencia }}</option>
                                    </select>
                                    <div class="invalid-feedback" v-show="errors.has('id_subcontrato')">{{ errors.first('id_subcontrato') }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group error-content">
                                    <label for="id_estimacion">Estimación</label>
                                    <select
                                            type="text"
                                            name="id_estimacion"
                                            data-vv-as="Estimacion"
                                            v-validate="{required: true}"
                                            class="form-control"
                                            id="id_estimacion"
                                            v-model="id_estimacion"
                                            :class="{'is-invalid': errors.has('id_estimacion')}"
                                    >
                                        <option value>-- Estimación --</option>
                                        <option v-for="tipo in estimaciones" :value="tipo.id">{{ tipo.referencia }}</option>
                                    </select>
                                    <div class="invalid-feedback" v-show="errors.has('id_estimacion')">{{ errors.first('id_estimacion') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Ver Formato</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- /.card -->
    </div>
</template>

<script>
    export default {
        name: "orden-pago-estimacion-index",
        data() {
            return {
                id_empresa: '',
                id_subcontrato:  '',
                id_estimacion: '',
                subcontratos: [],
                estimaciones: []
            }
        },
        mounted() {
            this.getEmpresas();
        },
        computed: {
            empresas() {
                return this.$store.getters['cadeco/empresa/empresas']
            },
        },
        methods: {

            getEmpresas() {
                return this.$store.dispatch('cadeco/empresa/index', {
                    params: {scope: 'paraSubcontratistas'}
                })
            },

            getSubcontratos(){
                return this.$store.dispatch('cadeco/empresa/find', {
                    id: this.id_empresa,
                    params: { include: 'subcontratos' }
                })
                    .then(data => {
                        this.subcontratos = data.subcontratos;
                    })
            },

            getEstimaciones(){
                return this.$store.dispatch('cadeco/subcontrato/find', {
                    id: this.id_subcontrato,
                    params: {
                        include: 'estimaciones'
                    }
                })
                    .then(data => {
                        this.estimaciones = data.estimaciones.data;
                    })
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store()
                    }
                });
            }
        },
        watch: {
             id_empresa(value) {
                 this.subcontratos = []
                 if (value) {
                     this.getSubcontratos();
                 }
             },
             id_subcontrato(value){
                 this.estimaciones = []
                 if(value){
                     this.getEStimaciones();
                 }
             }
        }
    }
</script>