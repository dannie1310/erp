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
        name: "IndexInvitacionProveedor",
        components: {},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', thClass:"th_index_corto", sortable: false },
                    { title: 'Tipo de Invitación', field: 'tipo_str', tdClass: 'td_c100' },
                    { title: 'Folio de Invitación', field: 'id', tdClass: 'td_c100', thComp: require('../../globals/th-Filter').default, sortable: true },
                    { title: 'Fecha de Invitación', field: 'fecha_hora_invitacion', tdClass: 'td_c100', thComp: require('../../globals/th-Date').default, sortable: true },
                    { title: 'Fecha de Cierre', field: 'fecha_cierre_invitacion', tdClass: 'td_c100', sortable: true, thComp: require('../../globals/th-Date').default },
                    { title: 'Proyecto', field: 'descripcion_obra', thComp: require('../../globals/th-Filter').default, sortable: true },
                    { title: 'Usuario Invitó', field: 'usuario_invito', sortable: true },
                    { title: 'Estado', field: 'estado', sortable: false, tdClass: 'th_c120', tdComp: require('./partials/EstatusLabel').default},
                    { title: 'Acciones', field: 'buttons', tdClass: 'td_c80',  tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {include: ['cotizacion'], sort: 'id',  order: 'desc', scope:['invitadoAutenticado']},
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
                return this.$store.dispatch('padronProveedores/invitacion/paginate', {
                    params: this.query
                })
                    .then(data => {
                        this.$store.commit('padronProveedores/invitacion/SET_INVITACIONES', data.data);
                        this.$store.commit('padronProveedores/invitacion/SET_META', data.meta);
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
                            color: '#e50c25',
                            text_color:'#f5f1f1',
                            descripcion: 'Recibida'
                        }
                    case 1:
                        return {
                            color: '#f36112',
                            text_color:'#000000',
                            descripcion: 'Leída'
                        }
                    case 2:
                        return {
                            color: '#f39c12',
                            text_color:'#000000',
                            descripcion: 'Cotizada'
                        }
                    case 3:
                        return {
                            color: '#59a153',
                            text_color:'#000000',
                            descripcion: 'Atendida'
                        }
                }
            },
        },
        computed: {
            invitaciones(){
                return this.$store.getters['padronProveedores/invitacion/invitaciones'];
            },

            meta(){
                return this.$store.getters['padronProveedores/invitacion/meta'];
            },

            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            invitaciones: {
                handler(invitaciones) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = invitaciones.map((invitacion, i) => ({
                        index: (i + 1) + self.query.offset,
                        razon_social:invitacion.nombre_usuario_invitado,
                        id: invitacion.numero_folio_format,
                        numero_folio_solicitud: invitacion.transaccion.numero_folio_format,
                        fecha_hora_invitacion: invitacion.fecha_format,
                        fecha_solicitud: invitacion.transaccion.fecha_format,
                        fecha_cierre_invitacion: invitacion.fecha_cierre_format,
                        usuario_invito: invitacion.nombre_usuario_invito,
                        descripcion_obra: invitacion.descripcion_obra,
                        estado: this.getEstado(invitacion.estado),
                        tipo_str: invitacion.tipo_str,
                        tipo: invitacion.tipo,
                        buttons: $.extend({}, {
                            id: invitacion.id,
                            con_cotizacion: invitacion.con_cotizacion,
                            show: true,
                            tipo_antecedente: invitacion.tipo_antecedente,
                            tipo: invitacion.tipo,
                            editar_cotizacion: (self.$root.can('editar_cotizacion_proveedor',true)  && invitacion.estado < 3 && invitacion.con_cotizacion) ? true : false,
                            registrar_cotizacion: (self.$root.can('registrar_cotizacion_proveedor',true) && invitacion.estado < 3 && !invitacion.con_cotizacion) ? true : false,
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
                handler () {
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

<style scoped>

</style>
