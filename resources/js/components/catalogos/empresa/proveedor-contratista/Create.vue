<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_proveedor')" class="btn btn-app btn-info float-right">
            <i class="fa fa-plus"></i> Registrar
        </button>

        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Registrar Proveedor / Contratista</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="razon_social" class="col-sm-2 col-form-label">Razón Social: </label>
                                        <div class="col-sm-10">
                                            <input
                                                    type="text"
                                                    name="razon_social"
                                                    data-vv-as="Razón Social"
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
                                <div class="col-md-6">
                                    <div class="form-group row error-content">
                                        <label for="rfc" class="col-sm-5 col-form-label">R.F.C.: </label>
                                        <div class="col-sm-7">
                                            <input
                                                    type="text"
                                                    name="rfc"
                                                    data-vv-as="R.F.C."
                                                    v-validate="{required: true}"
                                                    class="form-control"
                                                    id="rfc"
                                                    placeholder="R.F.C."
                                                    v-model="rfc"
                                                    :class="{'is-invalid': errors.has('rfc')}">
                                            <div class="invalid-feedback" v-show="errors.has('rfc')">{{ errors.first('rfc') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row error-content">
                                        <label for="no_proveedor_virtual" class="col-sm-5 col-form-label">No. Proveedor Virtual: </label>
                                        <div class="col-sm-7">
                                            <input
                                                    type="number"
                                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                    name="no_proveedor_virtual"
                                                    data-vv-as="No. Proveedor Virtual"
                                                    v-validate="{}"
                                                    class="form-control"
                                                    id="no_proveedor_virtual"
                                                    placeholder="No. Proveedor Virtual"
                                                    v-model="no_proveedor_virtual"
                                                    :class="{'is-invalid': errors.has('no_proveedor_virtual')}">
                                            <div class="invalid-feedback" v-show="errors.has('no_proveedor_virtual')">{{ errors.first('no_proveedor_virtual') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row error-content">
                                        <label for="dias_credito" class="col-sm-5 col-form-label">Días Crédito: </label>
                                        <div class="col-sm-7">
                                            <input
                                                    type="number"
                                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                    name="dias_credito"
                                                    data-vv-as="Días Crédito"
                                                    v-validate="{min_value:0, max_value:365, decimal:0}"
                                                    class="form-control"
                                                    id="dias_credito"
                                                    placeholder="Días Crédito"
                                                    v-model="dias_credito"
                                                    :class="{'is-invalid': errors.has('dias_credito')}">
                                            <div class="invalid-feedback" v-show="errors.has('dias_credito')">{{ errors.first('dias_credito') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row error-content">
                                        <label for="porcentaje" class="col-sm-5 col-form-label">Descuento Financiero: </label>
                                        <div class="col-sm-7">
                                            <input
                                                    type="number"
                                                    step="any"
                                                    name="porcentaje"
                                                    data-vv-as="Descuento Financiero"
                                                    v-validate="{min_value:0, max_value:100, decimal:2}"
                                                    class="form-control"
                                                    id="porcentaje"
                                                    placeholder="Descuento Financiero"
                                                    v-model="porcentaje"
                                                    :class="{'is-invalid': errors.has('porcentaje')}">
                                            <div class="invalid-feedback" v-show="errors.has('porcentaje')">{{ errors.first('porcentaje') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                   <div class="form-group row error-content">
                                        <label for="id_empresa" class="col  sm-3 col-form-label">Tipo Proveedor y/o Contratista: </label>
                                        <div class="col-sm-9">
                                            <div class="btn-group btn-group-toggle">
                                                <label class="btn btn-outline-secondary" :class="tipo_empresa === Number(key) ? 'active': ''" v-for="(tipo, key) in tipos_empresas()" :key="key">
                                                    <input type="radio"
                                                        class="btn-group-toggle"
                                                        name="id_tipo_empresa"
                                                        :id="'tipo_empresa' + key"
                                                        :value="key"
                                                        autocomplete="on"
                                                        v-model.number="tipo_empresa">
                                                    {{ tipo }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                           <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>           
</template>

<script>
export default {
    name: "proveedor-contratista-create",
    data() {
        return {
            razon_social:'',
            rfc:'',
            no_proveedor_virtual:'',
            dias_credito:'',
            porcentaje:'',
            tipo_empresa:'',
            tipo_cliente:0,
        }
    },
    mounted() {
    },
    computed: {

    },
    methods: {
        init() {
            $(this.$refs.modal).modal('show');
            this.$validator.reset();
            this.reset_fields();
        },
        reset_fields(){
            this.razon_social = '';
            this.rfc = '';
            this.no_proveedor_virtual = '';
            this.dias_credito = '';
            this.porcentaje = '';
            this.tipo_empresa = '';
        },
        store(){
            return this.$store.dispatch('cadeco/proveedor-contratista/store', this.$data)
                .then((data) => {
                    $(this.$refs.modal).modal('hide');
                    this.$emit('created',data)
                })
        },
        tipos_empresas(){
            return {
                1: "Proveedor",
                2: "Contratista",
                3: "Proveedor y Contratista"
            };
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    if(this.tipo_empresa === ''){
                        swal('¡Error!', 'Seleccione un Tipo Proveedor y/o Contratista.', 'error')
                    }else{
                        this.store()
                    }
                }
            });
        },
},

}
</script>

<style>

</style>