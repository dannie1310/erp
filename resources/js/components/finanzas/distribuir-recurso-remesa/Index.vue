<template>
    <div class="row">
        <div class="col-12"  v-if="$root.can('registrar_solicitud_pago_anticipado')" :disabled="cargando">
            <button  @click="create" title="Crear" class="btn btn-app btn-info pull-right" >
                <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                <i class="fa fa-plus" v-else></i>
                Registrar Distribuir
            </button>
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
    </div>
</template>

<script>
    import Create from "./Create";
    export default {
        name: "distribuir-recurso-remesa-index",
        components: {Create},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Folio', field: 'folio', sortable: false },
                    { title: 'Remesa Liberada', field: 'remesa', sortable: false },
                    { title: 'Monto Distribuido', field: 'monto_autorizado', sortable: false },
                    { title: 'Estatus', field: 'estado', tdComp: require('./partials/DistribuirEstatus') },
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons') },
                ],
                data: [],
                total: 0,
                query: {},
                estado: "",
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
                return this.$store.dispatch('finanzas/distribuir-recurso-remesa/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('finanzas/distribuir-recurso-remesa/SET_DISTRIBUCIONES', data.data);
                        this.$store.commit('finanzas/distribuir-recurso-remesa/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            create() {
                this.$router.push({name: 'distribuir-recurso-remesa-create'});
            },
        },
        computed: {
            distribuciones(){
                return this.$store.getters['finanzas/distribuir-recurso-remesa/distribuciones'];
            },
            meta(){
                return this.$store.getters['finanzas/distribuir-recurso-remesa/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            distribuciones: {
                handler(distribuciones) {
                    let self = this
                    self.$data.data = []
                    distribuciones.forEach(function (distribucion, i) {

                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            folio: 'REM-'+distribucion.folio,
                            remesa: 'Año: '+distribucion.remesa_liberada.remesa.año+' Semana: '+distribucion.remesa_liberada.remesa.semana+' Remesa: '+distribucion.remesa_liberada.remesa.tipo+' ('+distribucion.remesa_liberada.remesa.folio+')',
                            monto_autorizado: '$'+(parseFloat(distribucion.monto_autorizado)).formatMoney(2,'.',','),
                            estado: distribucion.estado,
                            buttons: $.extend({}, {
                                show: true,
                                id: distribucion.id
                            })
                        })
                    });
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
            cargando(val) {
                $('tbody').css({
                    '-webkit-filter': val ? 'blur(2px)' : '',
                    'pointer-events': val ? 'none' : ''
                });
            }
        }
    }
</script>