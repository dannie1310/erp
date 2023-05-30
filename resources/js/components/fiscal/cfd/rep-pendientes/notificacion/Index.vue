<template>
    <span>
        <div class="row">
            <div class="col-12">
                <div class="card">

                <!-- /.card-header -->
                    <div class="card-body">
                        <label v-if="this.proveedor">{{ this.proveedor.proveedor}}</label>
                        <div class="table-responsive">
                            <datatable v-bind="$data" v-bind:class="'table-sm table-bordered'" v-bind:style="'font-size: 11px'" />
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="modal-footer">
                        <div class="pull-right">
                            <button type="button" class="btn btn-secondary" v-on:click="salir">
                                <i class="fa fa-angle-left"></i>
                                Regresar</button>
                        </div>
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>
        <!-- /.col -->
    </span>
</template>

<script>
export default {
    name: "index-notificacion-rep-proveedor",
    props: ['id_proveedor'],
    data() {
        return {
            cargando: false,
            HeaderSettings: false,
            columns: [
                { title: '#', field:'index', thClass:"th_index_corto", sortable: false},
                { title: 'Fecha', field: 'fecha_hora_registro', tdClass: 'td_fecha', sortable: true},
                { title: 'Envía', field: 'usuario_registro', sortable: true},
                { title: '# CFDI Detectados', field: 'cantidad_cfdi',tdClass: 'td_money80', sortable: true},
                { title: 'Monto CFDI Detectados', field: 'monto_mxn_cfdi',tdClass: 'td_money', sortable: true},
                { title: '# CFDI Atendidos', field: 'cfdi_atendidos',tdClass: 'td_money80', sortable: true},
                { title: '# CFDI Nuevos', field: 'cfdi_nuevos',tdClass: 'td_money80', sortable: true},
                { title: '# CFDI Cancelados', field: 'cfdi_cancelados',tdClass: 'td_money80', sortable: true},
                { title: 'Acciones', field: 'buttons', thClass: 'th_c60', tdComp: require('./partials/ActionButtons').default},
            ],
            data: [],
            total: 0,
            query: {
                include: ['destinatarios'],
                scope: ['porProveedor:'+this.id_proveedor], sort:'id', order: 'desc'
            },
            daterange: null,
        }
    },
    mounted(){

    },

    methods: {
        paginate(){
            this.$Progress.start();
            this.$store.commit('fiscal/proveedor-rep/SET_PROVEEDOR_REP', null);
            this.find();
            this.cargando=true;
            return this.$store.dispatch('fiscal/notificacion-rep/paginate', {params: this.query})
                .then(data=>{

                })
                .finally(()=>{
                    this.cargando=false;
                    this.$Progress.finish();
                })
        },
        find(){
            return this.$store.dispatch('fiscal/proveedor-rep/find', {
                id: this.id_proveedor,
                params: {
                }
            }).then(data => {
                this.$store.commit('fiscal/proveedor-rep/SET_PROVEEDOR_REP', data);
            })
        },
        salir(){
            this.$router.push({name: 'informe-rep-faltantes-proveedor'});
        },
    },
    computed: {
        notificaciones(){
            return this.$store.getters['fiscal/notificacion-rep/notificaciones'];
        },
        proveedor(){
            return this.$store.getters['fiscal/proveedor-rep/currentProveedorREP'];
        },
        meta(){
            return this.$store.getters['fiscal/notificacion-rep/meta']
        },
        tbodyStyle() {
            return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
        }
    },
    watch: {
        notificaciones: {
            handler(notificaciones) {
                let self = this
                self.$data.data = []
                notificaciones.forEach(function (notificacion, i) {
                    self.$data.data.push({
                        index: (i + 1) + self.query.offset,
                        fecha_hora_registro: notificacion.fecha,
                        usuario_registro: notificacion.envia,
                        cantidad_cfdi : notificacion.cantidad_cfdi_format,
                        monto_mxn_cfdi: notificacion.monto_cfdi_format,
                        cfdi_atendidos: notificacion.cfdi_atendidos_format,
                        cfdi_nuevos: notificacion.cfdi_nuevos_format,
                        cfdi_cancelados: notificacion.cfdi_cancelados_format,
                        buttons: $.extend({}, {
                            id: notificacion.id,
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

<style scoped>

</style>
