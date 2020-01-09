<template>
    <div class="row">
        <div class="col-12">
            <create @created="paginate()"></create>
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
        <span>
            <div class="modal fade" ref="modal" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> CONSULTA DE PROVEEDOR / CONTRATISTA</h5>
                            <button type="button" class="close" @click="closeModal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <nav>
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    <a aria-controls="nav-identificacion" aria-selected="true" class="nav-item nav-link active" data-toggle="tab" href="#nav-identificacion"
                                    id="nav-identificacion-tab" role="tab">Identificación</a>

                                    <a aria-controls="nav-sucursales" aria-selected="false" class="nav-item nav-link" data-toggle="tab" href="#nav-sucursales"
                                    id="nav-sucursales-tab" role="tab">Sucursales</a>

                                    <a aria-controls="nav-materiales" aria-selected="false" class="nav-item nav-link" data-toggle="tab" href="#nav-materiales" 
                                    id="nav-materiales-tab" role="tab"  >Materiales Suministrados</a>
                                </div>
                                <div class="tab-content" id="nav-tabContent">
                                    <div aria-labelledby="nav-identificacion-tab" class="tab-pane fade show active" id="nav-identificacion" role="tabpanel">
                                        <div class="col-12" v-if="proveedorContratista">
                                            <div class="invoice p-3 mb-3">
                                                <div class="row">
                                                    <div class="table-responsive col-12">
                                                        <table class="table table-striped">
                                                            <tbody>
                                                                <tr>
                                                                    <th class="align">Razón Social:</th>
                                                                    <td>{{proveedorContratista.razon_social}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th class="align">R.F.C.:</th>
                                                                    <td>{{proveedorContratista.rfc}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th class="align">No. de Proveedor Virtual:</th>
                                                                    <td>{{proveedorContratista.proveedor_virtual}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th class="align">Días de Crédito:</th>
                                                                    <td>{{proveedorContratista.dias_credito}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th class="align">Descuento Financiero:</th>
                                                                    <td>{{proveedorContratista.porcentaje}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th class="align">Tipo Proveedor y/o Contratista:</th>
                                                                    <td>{{proveedorContratista.tipo}}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div aria-labelledby="nav-sucursales-tab" class="tab-pane fade" id="nav-sucursales" role="tabpanel">
                                        <div class="col-12" v-if="proveedorContratista">
                                            <div class="invoice p-3 mb-3">
                                                <div class="row" v-if="proveedorContratista.sucursales">
                                                    <div class="table-responsive col-12">
                                                        <table class="table table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Descripción</th>
                                                                    <th>Dirección</th>
                                                                    <th>Ciudad</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr v-for="(sucursal, i) in proveedorContratista.sucursales.data">
                                                                    <td>{{i+1}}</td>
                                                                    <td>{{sucursal.descripcion}}</td>
                                                                    <td>{{sucursal.direccion}}</td>
                                                                    <td>{{sucursal.ciudad}}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div aria-labelledby="nav-materiales-tab" class="tab-pane fade" id="nav-materiales" role="tabpanel">
                                        <div class="col-12" v-if="proveedorContratista">
                                            <div class="invoice p-3 mb-3">
                                                <div class="row" v-if="proveedorContratista.suministrados">
                                                    <div class="table-responsive col-12">
                                                        <table class="table table-striped table-fixed">
                                                            <thead>
                                                                <tr>
                                                                    <th style="width: 15%;">Id</th>
                                                                    <th style="width: 85%;">Material</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr v-for="(material, i) in proveedorContratista.suministrados.data">
                                                                    <td style="width: 15%;">{{i+1}}</td>
                                                                    <td style="width: 85%; text-align: left">{{material.material.descripcion}}</td>
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
                            <button type="button" class="btn btn-danger" @click="closeModal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div> 
        </span>
    </div>
</template>
<script>
    import Create from "./Create";
    export default {
        name: "proveedor-contratista-index",
        components: {Create},
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
                cargando: false

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
            create() {
                this.$router.push({name: 'proveedor-contratista-create'});
            },
            closeModal(){
                $('.nav-tabs a[href="#nav-identificacion"]').tab('show');
                $(this.$refs.modal).modal('hide');
            }
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
                        $(this.$refs.modal).modal('show');
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
.table-fixed tbody {
    display:block;
    height:400px;
    overflow:auto;
}
.table-fixed thead, .table-fixed tbody tr {
    display:table;
    width:100%;
    text-align: start;
}
</style>
