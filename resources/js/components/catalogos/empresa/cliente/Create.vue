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
                                        <label for="descripcion" class="col-sm-2 col-form-label">Razón Social:</label>
                                        <div class="col-sm-10">
                                            <input
                                                    style="text-transform:uppercase;"
                                                    type="text"
                                                    name="Razón Social"
                                                    data-vv-as="razon_social"
                                                    v-validate="{required: true}"
                                                    class="form-control"
                                                    id="razon_social"
                                                    placeholder="Razón Social"
                                                    v-model="razon_social"
                                                    :class="{'is-invalid': errors.has('razon_social')}">
                                            <div class="invalid-feedback" v-show="errors.has('razon_social')">{{ errors.first('razon_social') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row ">
                                        <label for="rfc" class="col-md-2" ><b>RFC: </b> </label>
                                        <div class="col-md-10">
                                            <input class="form-control"
                                                   name="rfc"
                                                   data-vv-as="RFC"
                                                   v-model="rfc"
                                                   :class="{'is-invalid':rfcValidate}"
                                                   v-validate="{ required: true, regex: /\.(js|ts)$/ }"
                                                   id="rfc"
                                                   placeholder="RFC" :maxlength="16"/>
                                            <span class="text-danger" v-if="rfcValidate">RFC Inválido</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-primary":disabled="errors.count() > 0 ">Registrar</button>
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
                razon_social : '',
                rfc : '',
                tipo_cliente : '',
                rfcValidate: false
            }
        },
        methods: {
            init() {
                this.cargando = false;
                $(this.$refs.modal).modal('show');
                this.razon_social = '';
                this.rfc = '';
                this.tipo_cliente = '';
            },
            store() {
                return this.$store.dispatch('cadeco/cliente/store', this.$data)
                    .then(data => {
                        this.$emit('created', data);
                        $(this.$refs.modal).modal('hide');
                    }).finally( ()=>{
                        this.cargando = false;
                    });
            },
            validate() {
                this.$validator.validate().then(result => {
                    if(!rfcRegex.test(this.banco.rfc)){
                        return this.invalidRFC();
                    } else{
                        this.rfcValidate=false;
                    }

                    if (result) {
                        this.store()
                    }
                });
            },
            invalidRFC(){
                this.rfcValidate=true;
            }
        }
    }
</script>

<style scoped>

</style>
