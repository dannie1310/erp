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
            <div class="col-md-12" v-if="detalle_unificacion">
                <div class="invoice p-3 mb-3">
                    <div class="modal-header">
                        <div class="row">
                            <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> DETALLE DE LA UNIFICACIÓN</h5>
                        </div>               
                    </div>
                    <div class="modal-body">
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
                                 <div class="form-group row error-content" v-if="!actualizarTipoEmpresa(detalle_unificacion.tipo_empresa)">
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
                        <div class="row">
                            <div class="col-md-12  mt-3 " >
                                   <hr style="color: #0056b2; margin-top:auto;" width="95%" size="10" />
                            </div>
                            <div class="col-md-12">
                                 <div class="form-group">
                                        <label><b>Detalle de Empresas a Unificar: </b></label>
                                        <button class="btn btn-primary float-right" @click="modalAgregar">
                                            Agregar
                                        </button>
                                    </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="table-responsive col-md-12">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Razón Social</th>
                                            <th>R.F.C.</th>
                                            <th>No. Transacciones Afectadas</th>
                                            <th>No. Cuentas Afectadas</th>
                                            <th>No. Solicitudes Cta. Afectadas</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                     <tbody>
                                        <tr v-for="(afectada, i) in detalle_unificacion.empresas_unificadas">
                                            <td>{{i+1}}</td>
                                            <td>{{afectada.razon_social}}</td>
                                            <td>{{afectada.rfc}}</td>
                                            <td>{{afectada.af_transacciones.total_afectacion}}</td>
                                            <td>{{afectada.af_cuentas}}</td>
                                            <td>{{afectada.af_solicitudesCBE}}</td>
                                            <td>
                                                <button @click="eliminar(i)" type="button" class="btn btn-sm btn-outline-danger  " title="Ver" style="margin-left:5px">
                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                </button>
                                            </td>
                                        </tr>
                                     </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                     <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="close()">Cerrar</button>
                        <button type="button" class="btn btn-primary" @click="validate" :disabled="detalle_unificacion.empresas_unificadas.length == 0">Unificar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> Agregar Empresa a Unificar</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <label for="id_empresa"  class="col-md-3">Seleccionar :</label>
                            <div class="col-md-9">
                                <model-list-select
                                    name="id_empresa"
                                    placeholder="Seleccionar o buscar por RFC o por razón social"
                                    data-vv-as="Orden de Compra"
                                    v-model="id_empresa_agregar"
                                    option-value="id"
                                    :custom-text="rfcAndRazonSocial"
                                    :list="empresas"
                                >
                                </model-list-select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-primary" @click="agregar" :disabled="id_empresa_agregar =='' || cargando">
                            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                            <i class="fa fa-plus" v-else></i>Agregar
                        </button>
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
            id_empresa_agregar:'',
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
        agregar(){
            let duplicada = false;
            let self = this;
            this.detalle_unificacion.empresas_unificadas.forEach(function(partida) {               
                    if(partida.id_empresa == self.id_empresa_agregar){
                        duplicada = true;
                    }
                });
            if(duplicada || this.id_empresa == this.id_empresa_agregar){
                swal('¡Error!', 'La empresa ya esta seleccionada para unificar', 'error')
            }else{
                self.cargando = true;
                 return this.$store.dispatch('cadeco/empresa/detalleEmpresaUnificacion', {
                    id:this.id_empresa_agregar,
                    params: {
                        // scope: '',
                        sort: 'rfc',
                        order: 'desc'
                    }  
                }).then(data => {
                    this.detalle_unificacion.empresas_unificadas.push(data);
                }).finally(()=>{
                    this.cargando = false;
                    $(this.$refs.modal).modal('hide')
                })
            }

        },
        close(){
            this.$router.push({name: 'unificacion-proveedores'});
        },
        eliminar(index){
            swal({
                    title: "Remover Empresa Unificada",
                    text: "¿Está seguro de que desea remover la empresa unificada del listado?",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Remover',
                            closeModal: true,
                        }
                    },
                    dangerMode: true,
                })
                .then((value) => {
                    if (value) {
                        this.detalle_unificacion.empresas_unificadas.splice(index, 1);
                    }
                });
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
                        scope: 'proveedorContratista',
                        sort: 'rfc',
                        order: 'desc'
                    }  
                }).then(data => {
                    this.empresas = data.data;
                }).finally(()=>{
                    this.cargando = false;
                })
        },
        modalAgregar(){
            this.id_empresa_agregar = '';
            $(this.$refs.modal).appendTo('body')
            $(this.$refs.modal).modal('show')
        },
        rfcAndRazonSocial (item){
            return `[${item.rfc}] - ${item.razon_social}`
        },
        unificar(){
            return this.$store.dispatch('catalogos/unificacion-proveedores/store', this.detalle_unificacion)
                .then((data) => {
                    this.$router.push({name: 'unificacion-proveedores'});
                });
        },
         validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    this.unificar();
                }
            });
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