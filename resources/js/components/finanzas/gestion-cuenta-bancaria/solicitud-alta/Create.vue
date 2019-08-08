<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_solicitud_pago_anticipado')" class="btn btn-app btn-info pull-right" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i>
            Registrar Solicitud
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> REGISTRAR SOLICITUD DE ALTA DE CUENTA BANCARIA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                     <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="btn-group btn-group-toggle">
                                        <label class="btn btn-outline-secondary" :class="id_tipo_solicitud === Number(key) ? 'active': ''" v-for="(tipo_solicitud, key) in tipos_solicitud" :key="key">
                                            <input type="radio"
                                                   class="btn-group-toggle"
                                                   name="id_tipo_solicitud"
                                                   :id="'tipo_solicitud_' + key"
                                                   :value="key"
                                                   autocomplete="off"
                                                   v-model.number="id_tipo_solicitud">
                                            {{ tipo_solicitud }}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="btn-group btn-group-toggle">
                                        <label class="btn btn-outline-secondary">
                                            <input type="radio"
                                                   class="btn-group-toggle"
                                                   name="tipo_empresa"
                                                   :id="1"
                                                   :value="key"
                                                   autocomplete="off"
                                                   v-model.number="tipo_empresa">prueba
                                        </label>
                                        <label class="btn btn-outline-secondary">
                                            <input type="radio"
                                                   class="btn-group-toggle"
                                                   name="tipo_empresa"
                                                   :id="1"
                                                   :value="key"
                                                   autocomplete="off"
                                                   v-model.number="tipo_empresa">prueba2
                                        </label>
                                    </div>
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
    import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale'
    export default {
        name: "solicitud-alta-create",
        components: {Datepicker},
        data() {
            return {
                es: es,
                cargando: false,
                tipo_empresa: '',
            }
        },
        computed: {

        },
        methods: {
            init() {
                this.cargando = true;
                $(this.$refs.modal).modal('show');
            },
            formatoFecha(date){
                return moment(date).format('YYYY-MM-DD');
            },
        },
        watch: {

        }
    }
</script>
<style scoped>
    .error-label {
        width: 100%;
        margin-top: 0.25rem;
        font-size: 80%;
        color: #dc3545;
    }
</style>