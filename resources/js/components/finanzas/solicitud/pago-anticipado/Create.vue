<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_solicitud_pago_anticipado')" class="btn btn-app btn-info pull-right" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i>
            Registrar Solicitud de Pago Anticipado
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">REGISTRAR SOLICITUD DE PAGO ANTICIPADO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <div class="form-group">
                                            <label><b>Fecha de Solicitud</b></label>
                                                <datepicker v-model = "fecha_solicitud"
                                                            name = "fecha_solicitud"
                                                            :language = "es"
                                                            :format = "formatoFecha"
                                                            :bootstrap-styling = "true"
                                                            class = "form-control"
                                                ></datepicker>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <div class="form-group">
                                            <label><b>Fecha Limite de Pago</b></label>
                                                <datepicker v-model = "fecha_limite"
                                                            name = "fecha_limite"
                                                            :language = "es"
                                                            :format = "formatoFecha"
                                                            :bootstrap-styling = "true"
                                                            class = "form-control"
                                                ></datepicker>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group error-content">
                                        <label for="tipo_transaccion">Tipo de Cuenta</label>
                                        <select
                                                type="text"
                                                name="tipo_transaccion"
                                                data-vv-as="Tipo Transacción"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="tipo_transaccion"
                                                v-model="tipo_transaccion"
                                                :class="{'is-invalid': errors.has('tipo_transaccion')}"
                                        >
                                            <option value>&#45;&#45; Tipo de Transacción &#45;&#45;</option>
                                            <option value="1">Orden de Compra</option>
                                            <option value="2">Orden de Compra</option>
                                            <option value="3">Orden de Compra</option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('tipo_transaccion')">{{ errors.first('tipo_transaccion') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group error-content">
                                        <label for="id_transaccion">Transacción</label>
                                        <select
                                                :disabled="!tipo_transaccion"
                                                type="text"
                                                name="id_transaccion"
                                                data-vv-as="Transacción"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="id_transaccion"
                                                v-model="id_transaccion"
                                                :class="{'is-invalid': errors.has('id_transaccion')}"
                                        >
                                            <option value>-- Seleccione Transacción --</option>
                                            <option v-for="transaccion in transacciones" :value="transaccion.id">{{ transaccion.descripcion }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('id_transaccion')">{{ errors.first('id_transaccion') }}</div>
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
    </span>
</template>

<script>
    import Datepicker from 'vuejs-datepicker';
    export default {
        name: "solicitud-pago-anticipado-create",
        components: {Datepicker},
        data() {
            return {
                fecha_solicitud: '',
                fecha_limite: '',
                tipo_transaccion: 0,
                cargando: false
            }
        },
        computed: {
            datosContables() {
                return this.$store.getters['auth/datosContables']
            }
        },
        methods: {
            init() {
                if (!this.datosContables) {
                    swal('¡Error!', 'No es posible registrar la cuenta debido a que no se ha configurado el formato de cuentas de la obra.', 'error')
                } else {
                    this.cargando = true;
                    $(this.$refs.modal).modal('show');


                    this.$validator.reset()
                    this.cargando = false;
                }
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