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
                                                    <label for="razon_social" class="col-sm-2 col-form-label">Razón Social: </label>
                                                    <div class="col-sm-10">
                                                        <input
                                                            :disabled="!$root.can('editar_proveedor_razon_social')"
                                                            type="text"
                                                            name="razon_social"
                                                            data-vv-as="Razón Social"
                                                            v-validate="{required: true}"
                                                            class="form-control"
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
                                                    <label for="id_empresa" class="col  sm-3 col-form-label">Tipo Proveedor y/o Contratista: </label>
                                                    <div class="col-sm-9">
                                                        <div class="btn-group btn-group-toggle">
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
                                            <div class="col-md-12">
                                                <button type="submit" class="btn btn-primary">Acturalizar</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div aria-labelledby="nav-edit-sucursales-tab" class="tab-pane fade" id="nav-edit-sucursales" role="tabpanel">
                                    <div class="row" v-if="proveedorContratista && $root.can('editar_sucursal_proveedor')" style="height:350px;" >
                                        <div class="col-12" v-if="sucursales">
                                            <div class="invoice p-3 mb-3">
                                                <div class="row" v-if="proveedorContratista.sucursales">
                                                    <div class="table-responsive col-12">
                                                        <table class="table table-striped table-fixed">
                                                            <thead>
                                                                <tr>
                                                                    <th style="width:5%;">#</th>
                                                                    <th style="width:35%;">Descripción</th>
                                                                    <th style="width:30%;">Dirección</th>
                                                                    <th style="width:15%;">Ciudad</th>
                                                                    <th style="width:15%;"></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr v-for="(sucursal, i) in sucursales">
                                                                    <td style="width:5%;">{{i+1}}</td>
                                                                    <td style="width:35%;">{{sucursal.descripcion}}</td>
                                                                    <td style="width:30%;">{{sucursal.direccion}}</td>
                                                                    <td style="width:15%;">{{sucursal.ciudad}}</td>
                                                                    <td style="width:15%;">
                                                                        <button type="button" class="btn btn-sm btn-outline-danger" @click="deleteSucursal(i)" title="Eliminar">
                                                                            <i class="fa fa-trash"></i>
                                                                        </button>
                                                                        <button type="button" class="btn btn-sm btn-outline-primary" title="Editar">
                                                                            <i class="fa fa-pencil"></i>
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <create-sucursal @created="updateSucursal" v-bind:id="proveedorContratista.id"></create-sucursal>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div aria-labelledby="nav-edit-materiales-tab" class="tab-pane fade" id="nav-edit-materiales" role="tabpanel">
                                    <div class="col-12" v-if="proveedorContratista" style="height:350px;">
                                        <div class="invoice p-3 mb-3">
                                            <div class="row" v-if="proveedorContratista.suministrados">
                                                <div class="table-responsive col-12">
                                                    <table class="table table-striped table-fixed">
                                                        <thead>
                                                            <tr>
                                                                <th style="width:10%;">#</th>
                                                                <th style="width:80%;">Material</th>
                                                                <th style="width:10%;"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(material, i) in proveedorContratista.suministrados.data">
                                                                <td style="width:10%;">{{i+1}}</td>
                                                                <td style="width:80%; text-align: left">{{material.material.descripcion}}</td>
                                                                <td style="width:10%;">
                                                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                                        <i class="fa fa-trash"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>   
                                </div>   
                            
                            </div>
                        </nav>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="closeModal">Cerrar</button>
                        <!-- <button type="submit" class="btn btn-primary">Guardar</button> -->
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import CreateSucursal from './partials/CreateSucursal';
export default {
    name: "proveedor-contratista-edit",
    components: {CreateSucursal},
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
            // sucursales:[],
            // materiales:[],
        }
    },
    methods: {
        closeModal(){
            this.$store.commit('cadeco/proveedor-contratista/SET_PROVEEDOR_CONTRATISTA', null);
            $('.nav-tabs a[href="#nav-edit-identificacion"]').tab('show');
            $(this.$refs.modalEdit).modal('hide');
        },
        deleteSucursal(id) {
            if(this.proveedorContratista.sucursales.data.length === 1){
                swal('¡Aviso!', 'El Proveedor / Contratista debe tener al menos una sucursal registrada.', 'warning')
            }else{

            }
        },
        fillDataEdit(){
            this.edit.razon_social = this.proveedorContratista.razon_social;
            this.edit.rfc = this.proveedorContratista.rfc;
            this.edit.no_proveedor_virtual = this.proveedorContratista.no_proveedor_virtual;
            this.edit.dias_credito = this.proveedorContratista.dias_credito;
            this.edit.porcentaje = this.proveedorContratista.porcentaje;
            this.edit.tipo_empresa = parseInt(this.proveedorContratista.tipo_empresa);

            this.$store.commit('cadeco/sucursal/SET_SUCURSALES', this.proveedorContratista.sucursales.data);

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
            console.log('Panda Update');
        },
        updateSucursal(data){
            this.$store.commit('cadeco/sucursal/INSERT_SUCURSAL', data);
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
        sucursales(){
            return this.$store.getters['cadeco/sucursal/sucursales'];
        }
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