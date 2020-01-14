<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_familia_maquinaria')" class="btn btn-app btn-info float-right" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i>
            Registrar
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> REGISTRAR CLIENTE</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="razon_social" class="col-sm-2 col-form-label">Razón Social:</label>
                                        <div class="col-sm-10">
                                            <input
                                                    style="text-transform:uppercase;"
                                                    type="text"
                                                    name="razon_social"
                                                    data-vv-as="Razón Social"
                                                    v-validate="{required: true}"
                                                    class="form-control"
                                                    id="razon_social"
                                                    placeholder="Razón Social"
                                                    v-model="registro_cliente.razon_social"
                                                    :class="{'is-invalid': errors.has('razon_social')}">
                                            <div class="invalid-feedback" v-show="errors.has('razon_social')">{{ errors.first('razon_social') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label for="rfc" class="col-md-2" ><b>RFC: </b> </label>
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
                                        <label for="tipo_cliente" class="col  sm-2 col-form-label">Tipo Cliente: </label>
                                        <div class="col-sm-10">
                                            <div class="btn-group btn-group-toggle">
                                                <label class="btn btn-outline-secondary" :class="registro_cliente.tipo_cliente === Number(key) ? 'active': ''" v-for="(tipo_cliente, key) in tipos_clientes" :key="key">
                                                    <input type="radio"
                                                        class="btn-group-toggle"
                                                        name="id_tipo_cliente"
                                                        :id="'tipo_cliente' + key"
                                                        :value="key"
                                                        autocomplete="on"
                                                        v-model.number="registro_cliente.tipo_cliente">
                                                        {{ tipo_cliente }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="porcentaje" class="col-sm-2 col-form-label">Porcentaje de Participación</label>
                                        <div class="col-sm-10">
                                            <input
                                                    type="number"
                                                    name="porcentaje"
                                                    data-vv-as="Porcentaje de Participación"
                                                    v-validate="{required: true, decimal:2, min_value:0, max_value: 100}"
                                                    class="form-control"
                                                    id="porcentaje"
                                                    placeholder="Porcentaje de Participación"
                                                    v-model="registro_cliente.porcentaje"
                                                    :class="{'is-invalid': errors.has('porcentaje')}">
                                            <div class="invalid-feedback" v-show="errors.has('porcentaje')">{{ errors.first('porcentaje') }}</div>
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
        name: "cliente-create",
        data() {
            return {
                cargando : false,
                registro_cliente : {
                    tipo_cliente : '',
                    razon_social : '',
                    rfc : '',
                    porcentaje : 0,
                },
                rfc : '',
                tipos_clientes: {
                    1: "Comprador",
                    2: "Inversionista",
                    3: "Comprador e Inversionista"
                },
                rfcValidate: false
            }
        },
        methods: {
            init() {
                this.$validator.reset();
                this.cargando = false;
                this.registro_cliente = {
                    tipo_cliente : '',
                        razon_social : '',
                        rfc : '',
                        porcentaje : 0,
                };
                this.rfc = '';
                $(this.$refs.modal).modal('show');
                this.rfcValidate =  false;
            },
            store() {
                return this.$store.dispatch('cadeco/cliente/store', this.$data.registro_cliente)
                    .then(data => {
                        if(typeof data.efo !== 'undefined'){
                            swal("El Cliente registrado se encuentra en el catálogo de efos con estado "+data.efo.ctg_estado.descripcion+".", {
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
                    this.registro_cliente.razon_social = this.registro_cliente.razon_social.toUpperCase();
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
                    this.registro_cliente.rfc = this.rfc
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
