<template>
    <div class="row">
        <div class="col-12">
            <router-link :to="{name: 'avance-subcontrato-seleccionar-create'}" v-if="$root.can('registrar_avance_subcontrato')" class="btn btn-app btn-info float-right" :disabled="cargando">
                <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                <i class="fa fa-plus" v-else></i>
                Registrar
            </router-link>
        </div>
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
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
                        <datatable v-bind="$data" v-bind:class="'table-sm table-bordered'" v-bind:style="'font-size: 11px'" />
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
        name: "avance-subcontrato-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Folio', field: 'numero_folio', thComp: require('../../globals/th-Filter').default, sortable: true},
                    { title: 'Fecha', field: 'fecha', sortable: true},
                    { title: 'Empresa', field: 'id_empresa', thComp:require('../../globals/th-Filter').default, sortable: true},
                    { title: 'Folio Subcontrato', field: 'subcontrato__numero_folio',  thComp:require('../../globals/th-Filter').default, sortable: true},
                    { title: 'Total', field: 'monto', thClass: 'th_c80', tdClass: 'td_money80', sortable: true},
                    { title: 'Estado', field: 'estado', sortable: false, tdClass: 'th_c100', tdComp: require('./partials/EstatusLabel').default},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {include: [ ], sort: 'id_transaccion', order: 'desc'},
                estado: "",
                search: '',
                cargando: false,
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
                return this.$store.dispatch('contratos/avance-subcontrato/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('contratos/avance-subcontrato/SET_AVANCES', data.data);
                        this.$store.commit('contratos/avance-subcontrato/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            getEstado(color, descripcion) {
                return {
                    color: color,
                    descripcion: descripcion
                }
            },
        },
        computed: {
            avances(){
                return this.$store.getters['contratos/avance-subcontrato/avances'];
            },
            meta(){
                return this.$store.getters['contratos/avance-subcontrato/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            avances: {
                handler(avances) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = avances.map((avance, i) => ({
                        index: (i + 1) + self.query.offset,
                        numero_folio: avance.numero_folio_format,
                        fecha: avance.fecha_format,
                        id_empresa: avance.empresa.razon_social,
                        subcontrato__numero_folio: avance.subcontrato.numero_folio_format,
                        monto: avance.total_format,
                        estado: this.getEstado(avance.color_estado, avance.descripcion_estado),
                        buttons: $.extend({}, {
                            id: avance.id,
                            edit : this.$root.can('editar_avance_subcontrato') ? true : false,
                            delete: (this.$root.can('eliminar_avance_subcontrato') && avance.estado == 0) ? true : false,
                        })
                    }));
                },
                deep: true
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
        }
    }
</script>
