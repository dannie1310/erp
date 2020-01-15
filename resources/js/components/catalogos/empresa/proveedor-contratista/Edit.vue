<template>
    <span>
        <div class="modal fade" ref="modalEdit" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> EDICIÓN DE PROVEEDOR / CONTRATISTA</h5>
                        <button type="button" class="close" @click="closeModal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <nav>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <a aria-controls="nav-identificacion" aria-selected="true" class="nav-item nav-link active" data-toggle="tab" href="#nav-edit-identificacion"
                                id="nav-edit-identificacion-tab" role="tab">Identificación</a>

                                <a aria-controls="nav-sucursales" aria-selected="false" class="nav-item nav-link" data-toggle="tab" href="#nav-edit-sucursales"
                                id="nav-edit-sucursales-tab" role="tab">Sucursales</a>

                                <a aria-controls="nav-materiales" aria-selected="false" class="nav-item nav-link" data-toggle="tab" href="#nav-edit-materiales" 
                                id="nav-edit-materiales-tab" role="tab"  >Materiales Suministrados</a>
                            </div>
                            <div class="tab-content" id="nav-tabContent">
                                
                                <div aria-labelledby="nav-edit-identificacion-tab" class="tab-pane fade show active" id="nav-edit-identificacion" role="tabpanel">
                                    <form role="form" @submit.prevent="validate"  style="height:350px;">
                                        <div class="row" v-if="proveedorContratista">
                                            <div class="col-md-12">
                                                <br>
                                                <div class="form-group row error-content">
                                                    <label for="razon_social" class="col-md-2 col-form-label">Razón Social: </label>
                                                    <div class="col-md-10">
                                                        <input style="width:94.7%; "
                                                            :disabled="!$root.can('editar_proveedor_razon_social')"
                                                            type="text"
                                                            name="razon_social"
                                                            data-vv-as="Razón Social"
                                                            v-validate="{required: true}"
                                                            class="form-control float-right"
                                                            id="razon_social"
                                                            placeholder="Razón Social"
                                                            v-model="edit.razon_social"
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
                                                            :disabled="!$root.can('editar_proveedor_rfc')"
                                                            type="text"
                                                            name="rfc"
                                                            data-vv-as="R.F.C."
                                                            v-validate="{required: true}"
                                                            class="form-control"
                                                            id="rfc"
                                                            placeholder="R.F.C."
                                                            v-model="edit.rfc"
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
                                                                v-model="edit.no_proveedor_virtual"
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
                                                                v-model="edit.dias_credito"
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
                                                                name="porcentaje"
                                                                data-vv-as="Descuento Financiero"
                                                                v-validate="{min_value:0, max_value:100, decimal:2}"
                                                                class="form-control"
                                                                id="porcentaje"
                                                                placeholder="Descuento Financiero"
                                                                v-model="edit.porcentaje"
                                                                :class="{'is-invalid': errors.has('porcentaje')}">
                                                        <div class="invalid-feedback" v-show="errors.has('porcentaje')">{{ errors.first('porcentaje') }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group row error-content">
                                                    <label for="id_empresa" class="col  sm-2 col-form-label">Tipo </label>
                                                    <div class="col-sm-10">
                                                        <div class="btn-group btn-group-toggle" style="margin-left:5%;">
                                                            <label class="btn btn-outline-secondary" 
                                                                :class="edit.tipo_empresa === Number(key) ? 'active': ''" 
                                                                v-for="(tipo, key) in tipos_empresas()" :key="key">
                                                                <input type="radio"
                                                                    class="btn-group-toggle"
                                                                    name="id_tipo_empresa"
                                                                    :id="'tipo_empresa' + key"
                                                                    :value="key"
                                                                    autocomplete="on"
                                                                    v-model.number="edit.tipo_empresa">
                                                                {{ tipo }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <br><br>
                                            <div class="col-md-12">
                                                <button type="submit" class="btn btn-primary float-right"><i class="fa fa-save"></i> Actualizar</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div aria-labelledby="nav-edit-sucursales-tab" class="tab-pane fade" id="nav-edit-sucursales" role="tabpanel">
                                    <sucursal-tab v-if="proveedorContratista" v-bind:id_empresa="proveedorContratista.id"></sucursal-tab>
                                </div>
                                <div aria-labelledby="nav-edit-materiales-tab" class="tab-pane fade" id="nav-edit-materiales" role="tabpanel">
                                    <material-tab v-if="proveedorContratista" v-bind:id_empresa="proveedorContratista.id"></material-tab>
                                </div>   
                            
                            </div>
                        </nav>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="closeModal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import SucursalTab from './EditTabs/SucursalTab'
import MaterialTab from './EditTabs/MaterialTab'
export default {
    name: "proveedor-contratista-edit",
    components: {SucursalTab, MaterialTab},
    props: ['tipo'],
    data(){
        return {
            id_empresa:'',
            edit:{
                razon_social:'',
                rfc:'',
                no_proveedor_virtual:'',
                dias_credito:'',
                porcentaje:'',
                tipo_empresa:'',
            },
        }
    },
    methods: {
        closeModal(){
            this.$store.commit('cadeco/proveedor-contratista/SET_PROVEEDOR_CONTRATISTA', null);
            $('.nav-tabs a[href="#nav-edit-identificacion"]').tab('show');
            $(this.$refs.modalEdit).modal('hide');
        },
        
        fillDataEdit(){
            this.edit.razon_social = this.proveedorContratista.razon_social;
            this.edit.rfc = this.proveedorContratista.rfc;
            this.edit.no_proveedor_virtual = this.proveedorContratista.proveedor_virtual;
            this.edit.dias_credito = this.proveedorContratista.dias_credito;
            this.edit.porcentaje = this.proveedorContratista.porcentaje;
            this.edit.tipo_empresa = this.proveedorContratista.tipo_empresa;

            this.$store.commit('cadeco/sucursal/SET_SUCURSALES', this.proveedorContratista.sucursales.data);
            this.$store.commit('cadeco/suministrado/SET_SUMINISTRADOS', this.proveedorContratista.suministrados.data);

            $(this.$refs.modalEdit).modal('show');
        },
        tipos_empresas(){
            return {
                1: "Proveedor",
                2: "Contratista",
                3: "Proveedor y Contratista"
            };
        },
        update(){
            return this.$store.dispatch('cadeco/proveedor-contratista/update', {
                    id: this.proveedorContratista.id,
                    data: this.edit,
                })
                .then(data => {
                    this.$store.commit('cadeco/proveedor-contratista/UPDATE_PROVEEDOR_CONTRATISTA', data);
                })
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    if(this.tipo_empresa === ''){
                        swal('¡Error!', 'Seleccione un Tipo Proveedor y/o Contratista.', 'error')
                    }else{
                        this.update()
                    }
                }
            });
        },
    },

    computed: {
        proveedorContratista() {
            return this.$store.getters['cadeco/proveedor-contratista/currentProveeedor'];
        }, 
    },
    watch:{
        tipo(value){
            if(value !== '' && value === 2){
                this.fillDataEdit();
            }
        }
    }

}
</script>

<style>
.align{
    text-align: left;
}
.table-fixed tbody {
    display:block;
    height:218px;
    overflow:auto;
}
.table-fixed thead, .table-fixed tbody tr {
    display:table;
    width:100%;
    text-align: start;
}
</style>