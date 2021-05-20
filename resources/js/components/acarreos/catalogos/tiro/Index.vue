<template>
    <div class="row">
        <div class="col-12">
            <Create @created="paginate()" />
            <DescargaLayout />
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
    import Create from './Create'
    import DescargaLayout from "./DescargaLayout";
    export default {
        name: "tiro-index",
        components: {Create,DescargaLayout},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', thClass: 'th_index', tdClass: 'td_index', sortable: false },
                    { title: 'Clave', field: 'clave',sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Descripción', field: 'descripcion', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Fecha Registro', field: 'fecha', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Concepto', field: 'concepto', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Estatus', field: 'estado_tiro', sortable: true, thClass:'th_c120', tdComp: require('./partials/EstatusLabel').default},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default}
                ],
                data: [],
                total: 0,
                query: {scope:'', include: ['concepto'], sort: 'IdTiro', order: 'asc'},
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
                return this.$store.dispatch('acarreos/tiro/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('acarreos/tiro/SET_TIROS', data.data);
                        this.$store.commit('acarreos/tiro/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            getEstado(estado, color) {
                return {
                    color: color,
                    descripcion: estado
                }
            },
        },
        computed: {
            tiros(){
                return this.$store.getters['acarreos/tiro/tiros'];
            },
            meta(){
                return this.$store.getters['acarreos/tiro/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            tiros: {
                handler(tiros) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = tiros.map((tiro, i) => ({
                        index: (i + 1) + self.query.offset,
                        clave: tiro.clave_format,
                        descripcion: tiro.descripcion,
                        fecha: tiro.fecha_registro_format,
                        estado_tiro: this.getEstado(tiro.estado_format, tiro.estado_color),
                        concepto: tiro.path__corta_concepto,
                        buttons: $.extend({}, {
                            id: tiro.id,
                            concepto: self.$root.can('editar_tiro') ? true : false,
                            activar: (tiro.estado === 0 && self.$root.can('editar_tiro')) ? true : false,
                            desactivar: (tiro.estado === 1 && self.$root.can('editar_tiro')) ? true : false,
                            show : self.$root.can('consultar_tiro') ? true : false,
                        })
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

<style scoped>

</style>
