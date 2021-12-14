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
                                            <input style="width:94.7%; "
                                                    type="text"
                                                    name="razon_social"
                                                    data-vv-as="Razón Social"
                                                    v-validate="{required: true}"
                                                    class="form-control float-right"
                                                    id="razon_social"
                                                    placeholder="Razón Social"
                                                    v-model="razon_social"
                                                    :class="{'is-invalid': errors.has('razon_social')}">
                                            <div class="invalid-feedback float-right"   v-show="errors.has('razon_social')"><span style="margin-left:5%;">{{ errors.first('razon_social') }}</span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row error-content">
                                        <label for="rfc" class="col-sm-5 col-form-label">RFC: </label>
                                        <div class="col-sm-7">
                                            <input
                                                :disabled="emite_factura === 0 || es_nacional === 0"
                                                type="text"
                                                name="rfc"
                                                data-vv-as="RFC"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="rfc"
                                                placeholder="RFC"
                                                v-model="rfc"
                                                :class="{'is-invalid': errors.has('rfc')}">
                                            <div class="invalid-feedback" v-show="errors.has('rfc')">{{ errors.first('rfc') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row error-content">
                                        <label for="no_proveedor_virtual" class="col-sm-5 col-form-label">No. de Proveedor Virtual: </label>
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
                                        <label for="dias_credito" class="col-sm-5 col-form-label">Días de Crédito: </label>
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
                                        <label for="id_empresa" class="col  sm- col-form-label">Tipo: </label>
                                        <div class="col-sm-10">
                                            <div class="btn-group btn-group-toggle" style="margin-left:5%;">
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
                                <div class="col-md-6">
                                   <div class="form-group row error-content">
                                        <label for="es_nacional" class="col-sm-5 col-form-label">¿Es Proveedor Nacional?: </label>
                                        <div class="col-sm-7">
                                            <div class="btn-group btn-group-toggle">
                                                <label class="btn btn-outline-secondary" :class="es_nacional === Number(1) ? 'active': ''"  :key="1">
                                                    <input type="radio"
                                                        class="btn-group-toggle"
                                                        name="es_nacional"
                                                        :id="'es_nacional' + 1"
                                                        :value="1"
                                                        autocomplete="on"
                                                        v-model.number="es_nacional">
                                                    Si
                                                </label>
                                                <label class="btn btn-outline-secondary" :class="es_nacional === Number(0) ? 'active': ''"  :key="0">
                                                    <input type="radio"
                                                        class="btn-group-toggle"
                                                        name="es_nacional"
                                                        :id="'es_nacional' + 0"
                                                        :value="0"
                                                        autocomplete="on"
                                                        v-model.number="es_nacional">
                                                    No
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                   <div class="form-group row error-content">
                                        <label for="emite_factura" class="col-sm-5 col-form-label">¿Emite Factura (XML)?: </label>
                                        <div class="col-sm-7">
                                            <div class="btn-group btn-group-toggle">
                                                <label class="btn btn-outline-secondary" :class="emite_factura === Number(1) ? 'active': ''"  :key="1">
                                                    <input type="radio" :disabled="es_nacional === 0"
                                                        class="btn-group-toggle"
                                                        name="emite_factura"
                                                        :id="'emite_factura' + 1"
                                                        :value="1"
                                                        autocomplete="on"
                                                        v-model.number="emite_factura">
                                                    Si
                                                </label>
                                                <label class="btn btn-outline-secondary" :class="emite_factura === Number(0) ? 'active': ''"  :key="0">
                                                    <input type="radio" :disabled="es_nacional === 0"
                                                        class="btn-group-toggle"
                                                        name="emite_factura"
                                                        :id="'emite_factura' + 0"
                                                        :value="0"
                                                        autocomplete="on"
                                                        v-model.number="emite_factura">
                                                    No
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
            emite_factura:1,
            es_nacional:1,
        }
    },
    mounted() {
    },
    computed: {

    },
    methods: {
        init() {
            $(this.$refs.modal).appendTo('body')
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
            this.emite_factura = 1;
            this.es_nacional = 1;
        },
        store(){
            return this.$store.dispatch('cadeco/proveedor-contratista/store', this.$data)
                .then((data) => {
                    if(data.efo !== null && (data.efo.estado.id == 0 || data.efo.estado.id == 2)){
                        swal("El Proveedor / Contratista registrado es un "+data.efo.estado.descripcion+" EFO.", {
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
        validar_emite(){
            if(this.emite_factura === 0 && this.es_nacional === 1){
                this.rfc = 'XXXXXXXXXXXX';
                this.emite_factura = 0;
            } else if(this.emite_factura === 0 && this.es_nacional === 0){
                this.rfc = 'XEXX010101000';
                this.emite_factura = 0;
            }
            else{
                this.rfc = '';
            }
        },
        validar_nacional(){
            if(this.es_nacional === 0){
                this.rfc = 'XEXX010101000';
                this.emite_factura = 0;
            }
            else{
                this.rfc = '';
            }
        }
    },
    watch:{
        es_nacional(value){
            if(value === 0){
                this.emite_factura = 0;
            }else{
                this.emite_factura = 1;
            }
        },
        emite_factura(value){
            if(value === 0 && this.es_nacional === 1){
                this.rfc = 'XXXXXXXXXXXX';
            }else if(value === 0 && this.es_nacional === 0){
                this.rfc = 'XEXX010101000';
            } else{
                this.rfc = '';
            }
        },
    }

}
</script>

<style>

</style>
