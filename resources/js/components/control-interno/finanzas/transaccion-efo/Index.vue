<template>
    <div class="row">
        <div class="col-12" :disabled="cargando">
            <button  @click="descarga_csv" title="Descargar" class="btn btn-app btn-info float-right"  >
<!--                     v-if="$root.can('consultar_transacciones_efos')" :disabled="cargando_csv" >-->
                <i class="fa fa-spin fa-spinner" v-if="cargando_csv"></i>
                <i class="fa fa-download" v-else></i>
                Descargar CSV
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
    export default {
        name: "transaccion-efos-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index',sortable: false },
                    { title: 'Obra', field: 'obra', sortable: false, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Razón Social', field: 'razon_social', sortable: false, thComp: require('../../../globals/th-Filter').default},
                    { title: 'RFC', field: 'rfc', sortable: false, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Transacción', field: 'tipo_transaccion', sortable: false},
                    { title: 'Folio', field: 'folio_transaccion', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Comentario', field: 'comentario', sortable: false},
                    { title: 'Usuario', field: 'id_usuario', sortable: false},
                    { title: 'Fecha Hora de Registro', field: 'fecha_hora_registro', sortable: false},
                    { title: 'Fecha Transacción', field: 'fecha_transaccion', sortable: true},
                    { title: 'Fecha Presunto', field: 'fecha_presunto', sortable: true},
                    { title: 'Fecha Definitivo', field: 'fecha_definitivo', sortable: true},
                    { title: 'Monto', field: 'monto', sortable: false},
                    { title: 'Moneda', field: 'moneda', sortable: false},
                    { title: 'T.C.', field: 'tipo_cambio', sortable: false},
                    { title: 'Monto MXN', field: 'monto_mxp', sortable: false},
                    { title: 'Grado de Alerta', field: 'grado_alerta', sortable: true}
                ],
                data: [],
                total: 0,
                query: { sort: 'fecha_transaccion', order: 'desc', include: 'usuario'},
                estado: "",
                cargando: false,
                cargando_csv: false
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
                return this.$store.dispatch('seguridad/finanzas/transaccion-efo/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('seguridad/finanzas/transaccion-efo/SET_TRANSACCIONES', data.data);
                        this.$store.commit('seguridad/finanzas/transaccion-efo/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            descarga_csv(){
                this.cargando_csv = true;
                return this.$store.dispatch('seguridad/finanzas/transaccion-efo/descarga_csv', {})
                    .then(() => {
                        this.$emit('success')

                    })
                    .finally(() => {
                        this.cargando_csv = false;
                    })
            }
        },
        computed: {
            transacciones(){
                return this.$store.getters['seguridad/finanzas/transaccion-efo/transacciones'];
            },
            meta(){
                return this.$store.getters['seguridad/finanzas/transaccion-efo/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            transacciones: {
                handler(famls) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = famls.map((transaccion, i) => ({
                        index: (i + 1) + self.query.offset,
                        obra: transaccion.obra,
                        razon_social: transaccion.razon_social,
                        rfc: transaccion.rfc,
                        tipo_transaccion: transaccion.tipo_transaccion,
                        folio_transaccion: transaccion.folio_transaccion,
                        comentario: transaccion.comentario,
                        id_usuario: (transaccion.usuario) ? transaccion.usuario.nombre : '---',
                        fecha_hora_registro: transaccion.fecha_hora_registro,
                        fecha_transaccion: transaccion.fecha_transaccion,
                        fecha_presunto: transaccion.fecha_presunto,
                        fecha_definitivo: transaccion.fecha_definitivo,
                        monto: transaccion.monto,
                        moneda: transaccion.moneda,
                        tipo_cambio: transaccion.tipo_cambio,
                        monto_mxp: transaccion.monto_mxp,
                        grado_alerta: transaccion.grado_alerta
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
    .money
    {
        text-align: right;
    }
</style>
