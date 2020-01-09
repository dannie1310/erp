<template>
    <div class="row">
        <div class="col-12">
            <create @created="paginate()"></create>
        </div>
        <div class="col-12">
            <show v-bind:tipo="tipo"></show>
        </div>
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <datatable v-bind="$data" />
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        
        <!-- /.col -->
        <!-- <span>
            <div class="modal fade" ref="modalEdit" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> EDICIÓN DE PROVEEDOR / CONTRATISTA</h5>
                            <button type="button" class="close" @click="closeModal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form role="form" @submit.prevent="validate">
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
                                            <div class="row" v-if="proveedorContratista">
                                                <div class="col-md-12">
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
                                                    <button type="button" class="btn btn-primary" @click="validate()" >Acturalizar</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div aria-labelledby="nav-edit-sucursales-tab" class="tab-pane fade" id="nav-edit-sucursales" role="tabpanel">
                                            <div class="row" v-if="proveedorContratista && $root.can('editar_sucursal_proveedor')">
                                                panda sucursal
                                            </div>
                                        </div>   
                                            </div>
                                        </div>   
                                            </div>
                                        </div>   
                                            </div>
                                        </div>
                                    </div>
                                </nav>
                            </nav> 
                                </nav>
                            </nav> 
                                </nav>
                            </div>
                            <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </span> -->
    </div>
</template>
<script>
    import Create from "./Create";
    import Show from "./Show";
    export default {
        name: "proveedor-contratista-index",
        components: {Create, Show},
        data(){
            return{
                HeaderSettings: false,
                columns: [
                    { title: '#', field:'index',sortable: false},
                    { title: 'R.F.C.', field: 'rfc',thComp: require('../../../globals/th-Filter').default, sortable: true},
                    { title: 'Razón Social', field: 'razon_social',thComp: require('../../../globals/th-Filter').default, sortable: true},
                    { title: 'Tipo', field: 'tipo_empresa', sortable: true},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {
                    sort: 'id_empresa',
                    order: 'desc'
                },
                cargando: false,
                id:'',
                tipo:''

            }
        },
        mounted() {
            this.$Progress.start();
            this.paginate()
                .finally(() => {
                    this.$Progress.finish();
                })
        },
        methods: {
            paginate(){
                this.cargando=true;
                return this.$store.dispatch('cadeco/proveedor-contratista/paginate', {params: this.query})
                    .then(data=>{
                        this.$store.commit('cadeco/proveedor-contratista/SET_PROVEEDOR_CONTRATISTAS', data.data);
                        this.$store.commit('cadeco/proveedor-contratista/SET_META',data.meta)
                    })
                    .finally(()=>{
                        this.cargando=false;
                    })

            },
        },
        computed: {
            proveedorContratistas(){
                return this.$store.getters['cadeco/proveedor-contratista/proveedorContratistas'];
            },
            proveedorContratista() {
                return this.$store.getters['cadeco/proveedor-contratista/currentProveeedor'];
            },
            meta(){
                return this.$store.getters['cadeco/proveedor-contratista/meta']
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            proveedorContratistas: {
                handler(proveedorContratistas) {
                    let self = this
                    self.$data.data = []
                    proveedorContratistas.forEach(function (proveedorContratista, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            rfc: proveedorContratista.rfc,
                            razon_social: proveedorContratista.razon_social,
                            tipo_empresa: proveedorContratista.tipo,
                            buttons: $.extend({}, {
                                id: proveedorContratista.id
                            })
                        })

                    });

                },
                deep: true
            },
            proveedorContratista:{
                handler(proveedorContratista) {
                    if(proveedorContratista !== null){
                        this.tipo = proveedorContratista.tipo;
                    }
                }
            },
            meta: {
                handler(meta) {
                    let total = meta.pagination.total
                    this.$data.total = total
                },
                deep: true
            },
            query: {
                handler(query) {
                    this.paginate(query)
                },
                deep: true
            },
            search(val) {
                if (this.timer) {
                    clearTimeout(this.timer);
                    this.timer = null;
                }
                this.timer = setTimeout(() => {
                    this.query.search = val;
                    this.query.offset = 0;
                    this.paginate();

                }, 500);
            },
            cargando(val) {
                $('tbody').css({
                    '-webkit-filter': val ? 'blur(2px)' : '',
                    'pointer-events': val ? 'none' : ''
                });
             }
        },
    }
</script>

<style scoped>

</style>
