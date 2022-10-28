<template>
    <div class="row">
        <div class="col-12">
            <router-link :to="{name: 'factura-seg-create'}" v-if="$root.can('registrar_factura_cuenta_x_cobrar', true)" class="btn btn-app btn-info float-right" :disabled="cargando">
                <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                <i class="fa fa-plus" v-else></i>
                Registrar
            </router-link>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Buscar" v-model="search">
                            </div>
                        </div>
                    </div>
                </div>
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
        <router-view ></router-view>
    </div>
</template>

<script>
    export default {
        name: "factura-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', thClass:"th_index_corto", sortable: false },
                    { title: 'Proyecto', field: 'idproyecto', tdClass: 'td_c250', sortable: true, thComp: require('../../globals/th-Filter').default},
                    { title: 'Número', tdClass: 'td_c100', field: 'numero', sortable: true, thComp: require('../../globals/th-Filter').default},
                    { title: 'Fecha de Registro', tdClass: 'td_c100', field: 'fecha', sortable: true, thComp: require('../../globals/th-Date').default},
                    { title: 'Empresa', tdClass: 'td_c100', field: 'idempresa', sortable: true, thComp: require('../../globals/th-Filter').default},
                    { title: 'Cliente', tdClass: 'td_c100', field: 'idcliente', sortable: true, thComp: require('../../globals/th-Filter').default},
                    { title: 'Descripción', tdClass: 'td_c100', field: 'descripcion',sortable: true, thComp: require('../../globals/th-Filter').default},
                    { title: 'Moneda', field: 'idmoneda', tdClass: 'td_c100', sortable: true, thComp: require('../../globals/th-Filter').default},
                    { title: 'Importe', field: 'importe', tdClass: 'money', sortable: true, thComp: require('../../globals/th-Filter').default},
                    { title: 'Fecha de Cobro', field: 'fecha_cobro', tdClass: 'money', sortable: true, thComp: require('../../globals/th-Date').default},
                    { title: 'Estatus', field: 'estado', tdClass: 'th_c120', tdComp: require('./partials/EstatusLabel').default, sortable: true},
                    { title: 'Acciones', field: 'buttons', thClass: 'th_m200', tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {include: [], sort: 'idfactura', order: 'desc'},
                search: '',
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
            paginate() {
                this.cargando = true;
                return this.$store.dispatch('seguimiento/factura/paginate', {
                    params: this.query
                })
                    .then(data => {
                        this.$store.commit('seguimiento/factura/SET_FACTURAS', data.data);
                        this.$store.commit('seguimiento/factura/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;

                    })
            },

            getEstado(descripcion, color) {
                return {
                    color: color,
                    descripcion: descripcion
                }

            },
        },
        computed: {
            facturas(){
                return this.$store.getters['seguimiento/factura/facturas'];
            },
            meta(){
                return this.$store.getters['seguimiento/factura/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            facturas: {
                handler(facturas) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = facturas.map((factura, i) => ({
                        index: (i + 1) + self.query.offset,
                        id: factura.id,
                        numero: factura.numero,
                        fecha: factura.fecha_format,
                        idproyecto: factura.nombre_proyecto,
                        idempresa : factura.nombre_empresa,
                        idcliente : factura.nombre_cliente,
                        descripcion : factura.descripcion,
                        idmoneda : factura.nombre_moneda,
                        importe : factura.importe_format,
                        fecha_cobro: factura.fecha_cobro_format,
                        estado: this.getEstado(factura.estado_descripcion, factura.estado_color),
                        buttons: $.extend({}, {
                            id: factura.id,
                            cancelar: (this.$root.can('cancelar_factura_cuenta_x_cobrar',true) && factura.estado == 1) ? true : false,
                        })
                    }));
                },
                deep: true
            },
            meta: {
                handler (meta) {
                    let total = meta.pagination.total
                    this.$data.total = total
                },
                deep: true
            },
            query: {
                handler (query) {
                    this.paginate()
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
        }
    }
</script>


<style>
    .folio
    {
        text-align: center;
    }
    .th_money
    {
        width: 150px;
        max-width: 150px;
        min-width: 100px;
    }
</style>
