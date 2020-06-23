<template>
    <span>
        <div class="row">
            <div class="col-md-12">
                <div class="invoice p-3 mb-3">
                    <div class="modal-body">
                        <div class="row">
                            <label for="id_empresa"  class="col-md-3">Seleccionar :</label>
                            <div class="col-md-9">
                                <model-list-select
                                    name="id_empresa"
                                    placeholder="Seleccionar o buscar por RFC o por razón social"
                                    data-vv-as="Orden de Compra"
                                    v-model="id_empresa"
                                    option-value="id"
                                    :custom-text="rfcAndRazonSocial"
                                    :list="empresas"
                                >
                                </model-list-select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="invoice p-3 mb-3">
                    <div class="modal-header">
                        <div class="row">
                            <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> DETALLE DE LA UNIFICACIÓN</h5>
                        </div>               
                    </div>
                    <div class="modal-body" v-if="detalle_unificacion">
                        <div class="row">
                            
                            <div class="col-md-6">
                                 <div class="form-group">
                                        <label><b>Empresa Unificadora: </b></label>
                                     {{ detalle_unificacion.razon_social }}
                                    </div>
                            </div>
                            <div class="col-md-6">
                                 <div class="form-group">
                                        <label><b>R.F.C.: </b></label>
                                     {{ detalle_unificacion.rfc }}
                                    </div>
                            </div>
                            <div class="col-md-6">
                                 <div class="form-group">
                                        <label><b>Tipo Empresa: </b></label>
                                     {{ detalle_unificacion.tipo_empresa_format }}
                                    </div>
                            </div>
                            <div class="col-md-6">
                                 <div class="form-group row error-content" v-if="actualizarTipoEmpresa(detalle_unificacion.tipo_empresa)">
                                        <label for="tipo_empresa_actualizado" class="col-md-4 col-form-label">Tipo Empresa Actualizado: </label>
                                        <div class="con-md-8">
                                         <select
                                                
                                                type="text"
                                                name="tipo_empresa_actualizado"
                                                data-vv-as="Tipo"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="tipo_empresa_actualizado"
                                                v-model="detalle_unificacion.tipo_empresa_actualizado"
                                                :class="{'is-invalid': errors.has('tipo_empresa_actualizado')}"
                                            >
                                                <option value>--SELECCIONE--</option>
                                                <option v-for="(tipo, i) in tipos_empresa" :value="i">{{ tipo }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('tipo_empresa_actualizado')">{{ errors.first('tipo_empresa_actualizado') }}</div>
                                        </div>
                                    </div>
                            </div>
                            
                        


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import {ModelListSelect} from 'vue-search-select';
export default {
    name: "unificacion-proveedores-create",
    components: {ModelListSelect},
    data() {
        return {
            cargando:false,
            empresas:[],
            id_empresa:'',
            detalle_unificacion:null,
            tipos_empresa:{
                1:'Proveedor',
                2:'Contratista',
                3:'Proveedor/Contratista',
            }
        }
    },
    mounted() {
        this.getEmpresas();
    },
    computed: {
        
    },
    methods: {
        actualizarTipoEmpresa(tipo){
            return tipo == 1 || tipo == 2 || tipo == 3;
        },
        getDetalleUnificacion(){
            this.cargando = true;
            this.detalle_unificacion = null;
            return this.$store.dispatch('cadeco/empresa/detalleUnificacion', {
                id:this.id_empresa,
                params: {
                    // scope: '',
                    sort: 'rfc',
                    order: 'desc'
                }  
            }).then(data => {
                this.detalle_unificacion = data;
            }).finally(()=>{
                this.cargando = false;
            })

        },
        getEmpresas(){
            this.cargando = true;
            return this.$store.dispatch('cadeco/empresa/index', {
                    params: {
                        // scope: '',
                        sort: 'rfc',
                        order: 'desc'
                    }  
                }).then(data => {
                    this.empresas = data.data;
                }).finally(()=>{
                    this.cargando = false;
                })
        },
        rfcAndRazonSocial (item){
            return `[${item.rfc}] - ${item.razon_social}`
        },

    },
    watch:{
        id_empresa(value){
            if(value != ''){
                this.getDetalleUnificacion();
            }
        }
    },

}
</script>

<style>

</style>