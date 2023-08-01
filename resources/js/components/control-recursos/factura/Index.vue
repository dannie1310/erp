<template>
    <div class="row">
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
    export default {
    name: "factura-index",
    data() {
        return {
            HeaderSettings: false,
            columns: [
                { title: '#', field: 'index', thClass: 'th_index', tdClass: 'td_index', sortable: false },
                { title: 'Serie', field: 'idserie',sortable: true, thComp: require('../../globals/th-Filter').default},
                { title: 'Tipo Documento', field: 'idtipodocto',sortable: true, thComp: require('../../globals/th-Filter').default},
                { title: 'Fecha', thClass: 'fecha_hora', field: 'fecha', sortable: true,thComp: require('../../globals/th-Date').default},
                { title: 'Folio', field: 'foliodocto',sortable: true, thComp: require('../../globals/th-Filter').default},
                { title: 'Concepto', field: 'concepto',sortable: true,thComp: require('../../globals/th-Filter').default},
                { title: 'Total', field: 'total', tdClass: 'money',sortable: true, thComp: require('../../globals/th-Filter').default},
                { title: 'Moneda', field: 'idmoneda',sortable: true, thComp: require('../../globals/th-Filter').default},

                //{ title: '', field: 'buttons', thClass: 'th_index',  tdComp: require('./partials/ActionButtons').default},
            ],
            data: [],
            total: 0,
            query: { sort: 'Fecha', order: 'desc'},
            estado: "",
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
            return this.$store.dispatch('controlRecursos/factura/paginate', { params: this.query})
                .then(data => {
                    this.$store.commit('controlRecursos/factura/SET_SOLICITUDES', data.data);
                    this.$store.commit('controlRecursos/factura/SET_META', data.meta);
                })
                .finally(() => {
                    this.cargando = false;
                })
        },
    },
    computed: {
        solicitudes(){
            return this.$store.getters['controlRecursos/factura/solicitudes'];
        },
        meta(){
            return this.$store.getters['controlRecursos/factura/meta'];
        },
        tbodyStyle() {
            return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
        }
    },
    watch: {
        solicitudes: {
            handler(solicitudes) {
                let self = this
                self.$data.data = []
                self.$data.data = solicitudes.map((solicitud, i) => ({
                    index: (i + 1) + self.query.offset,
                    fecha: solicitud.fecha,
                    concepto: solicitud.concepto,
                    foliodocto: solicitud.folio_format,
                    total: solicitud.total_format,
                    idmoneda: solicitud.moneda,
                    idserie: solicitud.serie,
                    idtipodocto: solicitud.tipo_documento,
                    /* buttons: $.extend({}, {
                        id: fecha.id,
                        eliminar: (self.$root.can('eliminar_fechas_inhabiles_sat',true)) ? true : false,
                    })*/
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

<style>

</style>
