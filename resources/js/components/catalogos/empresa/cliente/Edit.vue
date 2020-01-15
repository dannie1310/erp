<template>
    <span>
        <button @click="find(id)" type="button" class="btn btn-sm btn-outline-info" title="Editar"  :disabled="cargando">
            <i class="fa fa-pencil" v-if="!cargando"></i>
            <i class="fa fa-spinner fa-spin" v-else></i>
        </button>
        <!-- Modal -->
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">EDICIÓN DEL CLIENTE</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" v-if = "cliente" @submit.prevent="validate">
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
                                                    v-model="cliente.razon_social"
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
                                        <label for="tipo_cliente" class="col-md-2 col-form-label">Tipo Cliente: </label>
                                        <div class="col-md-10">
                                            <div class="btn-group btn-group-toggle">
                                                <label class="btn btn-outline-secondary" :class="cliente.tipo_cliente === Number(key) ? 'active': ''" v-for="(tipo_cliente, key) in tipos_clientes" :key="key">
                                                    <input type="radio"
                                                           class="btn-group-toggle"
                                                           name="tipo_cliente"
                                                           :id="'tipo_cliente' + key"
                                                           :value="key"
                                                           autocomplete="on"
                                                           v-model.number="cliente.tipo_cliente">
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
                                        <label for="porcentaje" class="col-md-2 col-form-label">Porcentaje de Participación</label>
                                        <div class="col-md-10">
                                            <input
                                                    type="number"
                                                    step="any"
                                                    name="porcentaje"
                                                    data-vv-as="Porcentaje de Participación"
                                                    v-validate="{required: true, min_value:0, max_value: 100, decimal:2}"
                                                    class="form-control"
                                                    id="porcentaje"
                                                    placeholder="Porcentaje de Participación"
                                                    v-model="cliente.porcentaje"
                                                    :class="{'is-invalid': errors.has('porcentaje')}">
                                            <div class="invalid-feedback" v-show="errors.has('porcentaje')">{{ errors.first('porcentaje') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                         <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
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
        name: "cliente-edit",
        props: ['id'],
        data() {
            return {
                cargando: false,
                rfc : '',
                cliente : null,
                tipos_clientes: {
                    1: "Comprador",
                    2: "Inversionista",
                    3: "Comprador e Inversionista"
                },
                rfcValidate: false
            }
        },
        methods: {
            find() {
                this.cargando = true;
                return this.$store.dispatch('cadeco/cliente/find', {
                    id: this.id
                })
                    .then(data => {
                        this.cliente = data
                        $(this.$refs.modal).modal('show');
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            validate() {
                this.cliente.razon_social = this.cliente.razon_social.toUpperCase()
                this.$validator.validate().then(result => {
                    if (result && this.rfcValidate == false) {
                        this.update()
                    }
                });
            },
            update() {
                return this.$store.dispatch('cadeco/cliente/update', {
                    id: this.id,
                    data: this.cliente
                })
                    .then(data => {
                        this.$store.commit('cadeco/cliente/UPDATE_CLIENTE', data);
                        if(typeof data.efo !== 'undefined' && (data.efo.estado.id == 0 || data.efo.estado.id == 2)){
                            swal("El Cliente registrado es un "+data.efo.estado.descripcion+" EFO.", {
                                icon: "warning",
                                buttons: {
                                    confirm: {
                                        text: 'Enterado',
                                        closeModal: true,
                                    }
            }
        }) .then(() => {
        $(this.$refs.modal).modal('hide');
    })
    }else {
        $(this.$refs.modal).modal('hide');
    }
    })
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
                    this.cliente.rfc = this.rfc
                    this.$validator.reset();
                }
            }
        },
        watch: {
            rfc(value) {
                this.rfc = this.rfc.toUpperCase();
                this.validateRfc();

            },
            cliente(value) {
                this.rfc = value.rfc;
            }
        }
    }
</script>

<style scoped>

</style>
