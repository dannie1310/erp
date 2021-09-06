<template>
    <div class="row">
        <div class="col-12"  v-if="$root.can('registrar_variacion_volumen')" :disabled="cargando">
            <button  @click="create" title="Crear" class="btn btn-app float-right" >
                <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                <i class="fa fa-plus" v-else></i>
                Registrar
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
    </div>
</template>

<script>
export default {
  name: "solicitud-cambio-index",
  components: {},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', thClass: 'th_index_corto', sortable: false },
                    { title: 'Folio', field: 'numero_folio', thClass:'th_c120', thComp: require('../../globals/th-Filter').default,  sortable: true},
                    { title: 'Fecha ', field: 'fecha', thClass: 'th_fecha', sortable: true  },
                    { title: 'Área Solicitante ', thClass:'th_c150', field: 'area_solicitante',  sortable: true  },
                    { title: 'Motivo ', field: 'motivo',  sortable: true  },
                    { title: 'Monto de la Afectación', field: 'monto_afectacion', tdClass: 'money', sortable: false },
                    { title: 'Porcentaje Cambio', field: 'porcentaje_cambio', tdClass: 'money', sortable: false },
                    { title: 'Estatus', field: 'estatus', thClass:'th_c100', sortable: false, tdComp: require('../../globals/EstatusLabel').default },
                    { title: 'Acciones', field: 'buttons', thClass:'th_c120',  tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {sort: 'id', order: 'desc'},
                search: '',
                cargando: false
            }
        },
        mounted() {
            // this.query.include = 'subcontrato.empresa';
            // this.query.sort = 'numero_folio';
            // this.query.order = 'DESC';
            this.$Progress.start();
            this.paginate()
                .finally(() => {
                    this.$Progress.finish();
                })
        },
        methods: {
          paginate() {
                this.cargando = true;
                return this.$store.dispatch('control-presupuesto/variacion-volumen/paginate', {
                    params: this.query
                })
                    .then(data => {
                        this.$store.commit('control-presupuesto/variacion-volumen/SET_VARIACIONES', data.data);
                        this.$store.commit('control-presupuesto/variacion-volumen/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            create() {
                this.$router.push({name: 'variacion-volumen-create'});

            },
        },
        computed: {
          solicitudes(){
                return this.$store.getters['control-presupuesto/variacion-volumen/variacionesVolumen'];
            },
            meta(){
                return this.$store.getters['control-presupuesto/variacion-volumen/meta'];
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
                            numero_folio: solicitud.numero_folio_format,
                            tipo_orden: solicitud.tipo_orden,
                            fecha: solicitud.fecha_solicitud_format,
                            usuario: solicitud.usuario.nombre,
                            motivo: solicitud.motivo,
                            area_solicitante : solicitud.area_solicitante,
                            monto_afectacion: solicitud.importe_afectacion_format,
                            porcentaje_cambio : solicitud.porcentaje_cambio_format,
                            estatus: solicitud.estado_label,
                            buttons: $.extend({}, {
                                id:solicitud.id,
                                estado: solicitud.id_estatus,
                                pdf: true,
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
            }
        },
}
</script>

<style>

</style>
