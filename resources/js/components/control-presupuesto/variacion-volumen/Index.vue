<template>
    <div class="row">
        <div class="col-12"  v-if="$root.can('registrar_variacion_volumen')" :disabled="cargando">
            <button  @click="create" title="Crear" class="btn btn-app btn-info float-right" >
                <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                <i class="fa fa-plus" v-else></i>
                Registrar
            </button>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
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
                    { title: 'Número de Folio', field: 'numero_folio', thComp: require('../../globals/th-Filter').default,  sortable: true},
                    { title: 'Tipo Orden', field: 'tipo_orden', sortable: true },
                    { title: 'Fecha Solicitud', field: 'fecha',  sortable: true  },
                    { title: 'Usuario Solicita', field: 'usuario', sortable: true  },
                    { title: 'Motivo', field: 'motivo',sortable: true },
                    { title: 'Importe Afectación', field: 'monto_afectacion',tdClass: 'money', sortable: false },
                    { title: 'Estatus', field: 'estatus', sortable: false },
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
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
                           numero_folio: solicitud.numero_folio,
                           tipo_orden: solicitud.tipo_orden,
                           fecha: solicitud.fecha_solicitud,
                           usuario: solicitud.usuario.nombre,
                           motivo: solicitud.motivo,
                           monto_afectacion: solicitud.importe_afectacion_format,
                           estatus: solicitud.estatus,
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