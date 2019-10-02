<template>
    <span>
        <div class="row">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                    <div class="row">
                        <div class="col-12">
                            <h4>
                                <i class="fa fa-list"></i> Registro de pagos
                            </h4>
                        </div>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row justify-content-between">
                                <div class="col-md-3">
                                    <label for="tipo" class="col-lg-12 col-form-label">Seleccionar Tipo</label>
                                    <select
                                        type="text"
                                        name="tipo"
                                        data-vv-as="Tipo"
                                        v-validate="{required: true}"
                                        class="form-control"
                                        id="tipo"
                                        v-model="tipo"
                                        :class="{'is-invalid': errors.has('id_remesa')}"
                                    >
                                            <option value>-- Seleccione un Tipo --</option>
                                            <option v-for="(tipo, index) in tipos" :value="index">{{tipo}}</option>
                                        </select>
                                </div>
                                <div class="col-md-8">

                                </div>
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
        name: "gestion-registro-pago",
        data() {
            return {
                tipos:{
                    0:'Facturas',
                    1:'Solicitudes'
                },
                tipo:'',
                data:[],
                cargando: false,
            }
        },
        mounted() {
        },
        computed: {
        },
        methods: {
            getDocumentos(tipo){
                console.log(tipo);
            }
        },
        watch: {
            tipo(value){
                if(value){
                    this.getDocumentos(value);
                }
            }
        }
    }
</script>

<style scoped>

</style>
