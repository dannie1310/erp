<template>
    <span>
        <div class="row">
            <div class="col-12">
                <router-link :to="{name: 'seleccionar-cfdi'}" v-if="$root.can('registrar_solicitud_recepcion_cfdi',true)" class="btn btn-app float-right" :disabled="cargando">
                    <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                    <i class="fa fa-plus" v-else></i>
                    Registrar
                </router-link>
            </div>
        </div>
        <div class="row" >
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
        </div>
            <!-- /.col -->
    </span>
</template>

<script>

    export default {
        name: "cfd-sat-index",

        data() {
            return {
                cargando: false,

                HeaderSettings: false,
                columns: [
                    { title: '#', field:'index',sortable: false},
                    { title: 'Fecha de Registro', field: 'fecha', sortable: true},
                    { title: 'Identificador', field: 'identificador', sortable: true},
                    { title: 'Folio', field: 'folio', sortable: true},
                    { title: 'Cliente', field: 'cliente'},
                    { title: 'UUID', field: 'uuid', tdComp: require('../fiscal/cfd/cfd-sat/UUID').default},
                    { title: 'Moneda', field: 'moneda'},
                    { title: 'Monto', field: 'monto', tdClass: 'td_money'},
                ],
                data: [],
                total: 0,
                query: {
                    include: ['empresa','cfdi'],
                    scope : ['porProveedorLogueado'],
                    sort: 'id',  order: 'desc'
                },
                daterange: null,
            }
        },
        mounted(){
            this.$Progress.start();
            this.paginate()
                .finally(() => {
                    this.$Progress.finish();
                })
        },

        methods: {
            paginate(){
                this.cargando=true;
                return this.$store.dispatch('entrega-cfdi/solicitud-recepcion-cfdi/paginate', {params: this.query})
                    .then(data=>{

                    })
                    .finally(()=>{
                        this.cargando=false;
                    })
            },
        },
        computed: {
            solicitudes(){
                return this.$store.getters['entrega-cfdi/solicitud-recepcion-cfdi/solicitudes'];
            },
            meta(){
                return this.$store.getters['entrega-cfdi/solicitud-recepcion-cfdi/meta']
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
                    solicitudes.forEach(function (solicitud, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            fecha: solicitud.fecha_registro,
                            identificador: solicitud.id,
                            folio: solicitud.numero_folio,
                            cliente: solicitud.empresa.razon_social,
                            moneda: solicitud.cfdi.moneda,
                            monto: solicitud.cfdi.total_format,
                            uuid: $.extend({}, {
                                id: solicitud.cfdi.id,
                                uuid: solicitud.cfdi.uuid,
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
            },
        },
    }
</script>

<style scoped>

</style>
