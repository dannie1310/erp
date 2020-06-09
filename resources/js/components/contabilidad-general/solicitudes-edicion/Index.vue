<template>
    <span>
        <div class="row">
            <div class="col-12"  :disabled="cargando">
                <button  @click="carga_masiva" title="Cargar solicitud de edición de póliza por layout" class="btn btn-app btn-info float-right" v-if="$root.can('registrar_solicitud_edicion_poliza_ctpq',true)" >
                    <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                    <i class="fa fa-upload" v-else></i>
                    Registrar por Layout
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <datatable v-bind="$data" />
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "index-solicitud-edicion-poliza",
        components: { },
        data() {
            return {
                cargando: false,
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', thClass: 'th_index_corto', sortable: false },
                    { title: 'Tipo', field: 'tipo', thClass: 'fecha_hora', thComp: require('../../globals/th-Filter').default, sortable: true},
                    { title: 'Folio', field: 'numero_folio', thClass: 'th_folio', thComp: require('../../globals/th-Filter').default, sortable: true},
                    { title: 'Fecha / Hora', field: 'fecha', thClass: 'fecha_hora', sortable: true},
                    { title: 'Usuario Registro', field: 'usuario_registro', sortable: true},
                    { title: '# Part.', field: 'numero_partidas', thClass: 'th_folio', sortable: true},
                    { title: '# BD', field: 'numero_bd', thClass: 'th_folio', sortable: true},
                    { title: '# Pol.', field: 'numero_polizas', thClass: 'th_folio', sortable: true},
                    { title: '# Mov.', field: 'numero_movimientos', thClass: 'th_folio', sortable: true},
                    { title: '# Cta.', field: 'numero_cuentas', thClass: 'th_folio', sortable: true},
                    { title: 'Estado', field: 'estado', sortable: true},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},

                ],
                data: [],
                total: 0,
                query: {sort: 'id', order: 'desc'},
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
            carga_masiva() {
                this.$router.push({name: 'solicitud-edicion-carga-masiva'});
            },
            paginate() {
                this.cargando = true;
                return this.$store.dispatch('contabilidadGeneral/solicitud-edicion-poliza/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('contabilidadGeneral/solicitud-edicion-poliza/SET_SOLICITUDES', data.data);
                        this.$store.commit('contabilidadGeneral/solicitud-edicion-poliza/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
        },
        computed: {
            solicitudes(){
                return this.$store.getters['contabilidadGeneral/solicitud-edicion-poliza/solicitudes'];
            },
            meta(){
                return this.$store.getters['contabilidadGeneral/solicitud-edicion-poliza/meta'];
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
                            numero_folio:solicitud.numero_folio_format,
                            fecha: solicitud.fecha_hora_registro_format,
                            usuario_registro: solicitud.usuario_registro,
                            tipo: solicitud.tipo_solicitud,
                            numero_bd: solicitud.numero_bd,
                            numero_polizas: solicitud.numero_polizas,
                            numero_partidas: solicitud.numero_partidas,
                            numero_movimientos: solicitud.numero_movimientos,
                            numero_cuentas: solicitud.numero_cuentas,
                            estado: solicitud.estado_format,
                            buttons: $.extend({}, {
                                id: solicitud.id,
                                estado: solicitud.estado,
                                autorizar: self.$root.can('autorizar_solicitud_edicion_poliza_ctpq',true) ? true : false,
                                rechazar: self.$root.can('rechazar_solicitud_edicion_poliza_ctpq',true) ? true : false,
                                aplicar: self.$root.can('aplicar_solicitud_edicion_poliza_ctpq',true) ? true : false,
                                show: true
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
        }
    }
</script>

<style scoped>

</style>