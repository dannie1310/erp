<template>
    <div class="row">
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
        name: "estimacion-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Folio Asignación', field: 'folio_asignacion',  sortable: true},
                    { title: 'Fecha Asignación', field: 'fecha_asignacion',  sortable: true},
                    { title: 'Folio Contrato proyectado', field: 'numero_folio', sortable: true },
                    { title: 'Fecha Contrato Proyectado', field: 'fecha',  sortable: true  },
                    { title: 'Referencia Contrato Proyectado', field: 'referencia', sortable: true  },
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {},
                search: '',
                cargando: false
            }
        },
        mounted() {
            this.query.include = 'contrato';
            this.query.sort = 'id_asignacion';
            this.query.order = 'DESC';

            this.$Progress.start();
            this.paginate()
                .finally(() => {
                    this.$Progress.finish();
                })
        },
        methods: {
            paginate() {
                this.cargando = true;
                return this.$store.dispatch('contratos/asignacion-contratista/paginate', {
                    params: this.query
                })
                    .then(data => {
                        this.$store.commit('contratos/asignacion-contratista/SET_ASIGNACIONES', data.data);
                        this.$store.commit('contratos/asignacion-contratista/SET_META', data.meta);
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
                            descripcion: 'Registrada'
                        }
                    case 1:
                        return {
                            color: '#0073b7',
                            descripcion: 'Aprobada'
                        }
                    case 2:
                        return {
                            color: '#00a65a',
                            descripcion: 'Revisada'
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
            asignaciones(){
                return this.$store.getters['contratos/asignacion-contratista/asignaciones'];
            },
            meta(){
                return this.$store.getters['contratos/estimacion/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            asignaciones: {
                handler(asignaciones) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = asignaciones.map((asignacion, i) => ({
                        index: (i + 1) + self.query.offset,
                        folio_asignacion: asignacion.numero_folio_asignacion,
                        fecha_asignacion: asignacion.fecha_registro,
                        numero_folio: asignacion.contrato.numero_folio_format,
                        fecha: asignacion.contrato.fecha,
                        referencia: asignacion.contrato.referencia,
                        buttons: $.extend({}, {
                            id: asignacion.id,
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