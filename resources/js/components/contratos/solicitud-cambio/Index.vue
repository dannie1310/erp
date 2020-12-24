<template>
    <div class="row">
        <div class="col-12">
            <router-link :to="{name: 'solicitud-cambio-create'}" v-if="$root.can('registrar_solicitud_cambio_subcontrato')" class="btn btn-app btn-info float-right" :disabled="cargando">
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
                { title: 'Número de Folio', field: 'numero_folio', thComp: require('../../globals/th-Filter').default,  sortable: true},
                { title: 'Observaciones', field: 'observaciones', sortable: true },
                { title: 'Contratista', field: 'id_empresa',  sortable: true  },
                { title: 'Subtotal', field: 'subtotal', tdClass: 'money', thClass: 'th_money', sortable: true  },
                { title: 'IVA', field: 'impuesto', tdClass: 'money', thClass: 'th_money', sortable: true },
                { title: 'Total', field: 'total', tdClass: 'money', thClass: 'th_money', sortable: false },
                { title: 'Estatus', field: 'estado', sortable: true},
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
        this.query.include = ['subcontrato.empresa','relaciones'];
        this.query.sort = 'numero_folio';
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
            return this.$store.dispatch('contratos/estimacion/paginate', {
                params: this.query
            })
                .then(data => {
                    this.$store.commit('contratos/estimacion/SET_ESTIMACIONES', data.data);
                    this.$store.commit('contratos/estimacion/SET_META', data.meta);
                })
                .finally(() => {
                    this.cargando = false;
                })
        },

    },
    computed: {
        estimaciones(){
            return this.$store.getters['contratos/estimacion/estimaciones'];
        },
        meta(){
            return this.$store.getters['contratos/estimacion/meta'];
        },
        tbodyStyle() {
            return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
        }
    },
    watch: {
        estimaciones: {
            handler(estimaciones) {
                let self = this
                self.$data.data = []
                self.$data.data = estimaciones.map((estimacion, i) => ({
                    index: (i + 1) + self.query.offset,
                    numero_folio: estimacion.numero_folio_format,
                    observaciones: estimacion.observaciones,
                    id_empresa: estimacion.subcontrato.empresa.razon_social,
                    estado: estimacion.estado,
                    total: estimacion.monto_pagar_format,
                    impuesto:estimacion.impuesto_format,
                    subtotal: estimacion.subtotal_orden_pago,
                    buttons: $.extend({}, {
                        aprobar: (this.$root.can('aprobar_estimacion_subcontrato') && estimacion.estado == 0 ) ? true : undefined,
                        desaprobar: (this.$root.can('revertir_aprobacion_estimacion_subcontrato') && estimacion.estado == 1 ) ? true : undefined ,
                        id: estimacion.id,
                        estimacion: estimacion,
                        estado: estimacion.estado,
                        delete: self.$root.can('eliminar_estimacion_subcontrato') ? true : false,
                        edit: self.$root.can('editar_estimacion_subcontrato') ? true : false,
                        transaccion: {id:estimacion.id, tipo:52},
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
