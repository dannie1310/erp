<template>
    <div class="row">
        <div class="col-md-12">
            <Registro @created="paginate()"></Registro>
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
    import Registro from "./partials/Registrar";
    export default {
        name: "estimacion-index",
        components:{Registro},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Número de Folio', field: 'numero_folio', sortable: true},
                    { title: 'Observaciones', field: 'observaciones', sortable: true },
                    { title: 'Contratista', field: 'id_empresa',  sortable: true  },
                    { title: 'Subtotal', field: 'subtotal', tdClass: 'money', thClass: 'th_money', sortable: false },
                    { title: 'IVA', field: 'impuesto', tdClass: 'money', thClass: 'th_money', sortable: true },
                    { title: 'Total', field: 'monto', tdClass: 'money', thClass: 'th_money', sortable: true },
                    { title: 'Estatus', field: 'estado', sortable: true, tdComp: require('./partials/EstatusLabel').default},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {sort: 'numero_folio', order: 'DESC', include:['relaciones']},
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
                return this.$store.dispatch('contratos/subcontrato/paginate', {
                    params: this.query
                })
                    .then(data => {
                        this.$store.commit('contratos/subcontrato/SET_SUBCONTRATOS', data.data);
                        this.$store.commit('contratos/subcontrato/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },

            getEstado(estado) {

                let val = parseInt(estado);
                switch (val) {
                    case 0:
                        return {
                            color: '#f39c12',
                            descripcion: 'Registrado'
                        }
                    case 1:
                        return {
                            color: '#0073b7',
                            descripcion: 'Estimado Parcial'
                        }
                    case 2:
                        return {
                            color: '#00a65a',
                            descripcion: 'Estimado Total'
                        }
                    default:
                        return {
                            color: '#d2d6de',
                            descripcion: 'Desconocido'
                        }
                }
            }
        },
        computed: {
            subcontratos(){
                return this.$store.getters['contratos/subcontrato/subcontratos'];
            },
            meta(){
                return this.$store.getters['contratos/subcontrato/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            subcontratos: {
                handler(subcontratos) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = subcontratos.map((subcontrato, i) => ({
                        index: (i + 1) + self.query.offset,
                        numero_folio: subcontrato.numero_folio_format,
                        observaciones: subcontrato.observaciones,
                        id_empresa: subcontrato.empresa,
                        estado: this.getEstado(subcontrato.estado),
                        monto: subcontrato.monto_format,
                        impuesto:subcontrato.impuesto_format,
                        subtotal: subcontrato.subtotal_format,
                        buttons: $.extend({}, {
                            show: true,
                            id: subcontrato.id,
                            transaccion: {id:subcontrato.id, tipo:51},
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
    .money
    {
        text-align: right;
    }
    .th_money
    {
        width: 150px;
        max-width: 150px;
        min-width: 100px;
    }
</style>
