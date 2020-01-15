<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_destajista')" class="btn btn-app btn-info float-right" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i>
            Registrar
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> REGISTRAR DESTAJISTA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="razon_social" class="col-md-2 col-form-label">Razón Social:</label>
                                        <div class="col-md-10">
                                            <input
                                                style="text-transform:uppercase;"
                                                type="text"
                                                name="razon_social"
                                                data-vv-as="Razón Social"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="razon_social"
                                                placeholder="Razón Social"
                                                v-model="registro_destajista.razon_social"
                                                :class="{'is-invalid': errors.has('razon_social')}">
                                            <div class="invalid-feedback" v-show="errors.has('razon_social')">{{ errors.first('razon_social') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label for="rfc" class="col-md-2"><b>RFC: </b> </label>
                                        <div class="col-md-10">
                                            <input class="form-control"
                                                   style="text-transform:uppercase;"
                                                   name="rfc"
                                                   data-vv-as="RFC"
                                                   v-model="rfc"
                                                   :class="{'is-invalid':rfcValidate}"
                                                   v-validate="{ required: true }"
                                                   id="rfc"
                                                   placeholder="RFC" :maxlength="16"/>
                                            <span class="text-danger" v-if="rfcValidate">RFC Inválido</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="dias_credito" class="col-md-2 col-form-label">Condición de Pago (días):</label>
                                        <div class="col-md-10">
                                            <input
                                                type="number"
                                                name="dias_credito"
                                                data-vv-as="Condición de Pago (días)"
                                                v-validate="{required: true,  min_value:0}"
                                                class="form-control"
                                                id="dias_credito"
                                                placeholder="Condición de Pago (días)"
                                                v-model="registro_destajista.dias_credito"
                                                :class="{'is-invalid': errors.has('dias_credito')}">
                                            <div class="invalid-feedback" v-show="errors.has('dias_credito')">{{ errors.first('dias_credito') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                <button type="button" class="btn btn-primary" :disabled="errors.count() > 0" v-on:click="validate">Registrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    const rfcRegex =/^([A-ZÑ&]{3,4}) ?(?:- ?)?(\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01])) ?(?:- ?)?([A-Z\d]{2})([A\d])$/;
    export default {
        name: "destajista-create",
        data() {
            return {
                cargando : false,
                registro_destajista : {
                    razon_social : '',
                    rfc : '',
                    dias_credito : 0,
                },
                rfc : '',
                rfcValidate: false
            }
        },
        methods: {
            init() {
                this.$validator.reset();
                this.cargando = false;
                this.registro_destajista = {
                    razon_social : '',
                    rfc : '',
                    dias_credito : 0,
                };
                this.rfc = '';
                $(this.$refs.modal).modal('show');
                this.rfcValidate =  false;
            },
            store() {
                return this.$store.dispatch('cadeco/destajista/store', this.$data.registro_destajista)
                    .then(data => {
                        if(typeof data.efo !== 'undefined' && (data.efo.estado.id == 0 || data.efo.estado.id == 2)){
                            swal("El Destajista registrado es un "+data.efo.estado.descripcion+" EFO.", {
                                icon: "warning",
                                buttons: {
                                    confirm: {
                                        text: 'Enterado',
                                        closeModal: true,
                                    }
                                }
                            }) .then(() => {
                                this.$emit('created', data);
                                $(this.$refs.modal).modal('hide');
                            })
                        }else {
                            this.$emit('created', data);
                            $(this.$refs.modal).modal('hide');
                        }

                    }).finally( ()=>{
                        this.cargando = false;
                    });
            },
            validate() {
                this.$validator.validate().then(result => {
                    this.registro_destajista.razon_social = this.registro_destajista.razon_social.toUpperCase();
                    if (result && this.rfcValidate == false) {
                        this.store()
                    }
                });
            },
            invalidRFC(){
                this.rfcValidate=true;
            },
            validateRfc()
            {
                if(!rfcRegex.test(this.rfc)){
                    return this.invalidRFC();
                } else{
                    this.rfcValidate=false;
                    this.registro_destajista.rfc = this.rfc
                    this.$validator.reset();
                }
            }
        },
        watch: {
            rfc(value) {
                this.rfc = this.rfc.toUpperCase();
                this.validateRfc();

            },
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
