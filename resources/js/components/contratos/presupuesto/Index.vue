<template>
    <div class="row">
        <div class="col-12">
            <button @click="create" v-if="$root.can('registrar_presupuesto_contratista')" class="btn btn-app btn-info pull-right">
                <i class="fa fa-plus"></i> Registrar
            </button>
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
    </div>
</template>

<script>
    export default {
        name: "presupuesto-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Núm de Folio', field: 'numero_folio', tdClass: 'folio', sortable: true},
                    { title: 'Fecha', field: 'fecha', sortable: true },
                    { title: 'Contratista', field: 'contratista', sortable: false },
                    { title: ' Referencia Contrato Proyectado ', field: 'observaciones', sortable: false },
                    { title: 'Importe', field: 'importe', tdClass: ['th_money', 'text-right'], sortable: false },
                    { title: 'Usuario Registró', tdClass: 'folio', field: 'usuario', sortable: false },
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {sort: 'numero_folio', order: 'DESC', include: ['contrato_proyectado', 'usuario', 'empresa']},
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
                return this.$store.dispatch('contratos/presupuesto/paginate', {
                    params: this.query
                })
                    .then(data => {
                        this.$store.commit('contratos/presupuesto/SET_PRESUPUESTOS', data.data);
                        this.$store.commit('contratos/presupuesto/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;

                    })
            },
            
            create() {
                this.$router.push({name: 'presupuesto-create'});
            },
        },
        computed: {
            presupuestos(){
                return this.$store.getters['contratos/presupuesto/presupuestos'];
            },
            meta(){
                return this.$store.getters['contratos/presupuesto/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            presupuestos: {
                handler(presupuestos) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = presupuestos.map((presupuesto, i) => ({
                        index: (i + 1) + self.query.offset,
                        numero_folio: presupuesto.numero_folio,
                        fecha: presupuesto.fecha_format,
                        contratista: (presupuesto.empresa) ? presupuesto.empresa.razon_social : '----- Proveedor Desconocido -----',
                        observaciones: (presupuesto.contrato_proyectado) ? presupuesto.contrato_proyectado.referencia : '----- Sin Contrato Proyectado -----',
                        importe: '$ ' + (parseFloat(presupuesto.subtotal) + parseFloat(presupuesto.impuesto)).formatMoney(2,'.',','),
                        usuario: (presupuesto.usuario) ? presupuesto.usuario.nombre : '---------------------------',
                        buttons: $.extend({}, {
                            id: presupuesto.id
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
