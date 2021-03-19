<template>
    <div class="row">
        <!-- <div class="col-12">
            <Create @created="paginate()" />
        </div>-->
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
        name: "origen-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', thClass: 'th_index', tdClass: 'td_index', sortable: false },
                    { title: 'Clave', field: 'clave',sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Tipo', field: 'tipo', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Descripción', field: 'descripcion', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Fecha Registro', field: 'fecha', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Tipo de origen', field: 'tipo_origen', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Estatus', field: 'estado_tiro', sortable: true, thClass:'th_c120', tdComp: require('./partials/EstatusLabel').default},
                    //{ title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default}
                ],
                data: [],
                total: 0,
                query: {scope:'', sort: 'IdOrigen', order: 'asc'},
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
                return this.$store.dispatch('acarreos/origen/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('acarreos/origen/SET_ORIGENES', data.data);
                        this.$store.commit('acarreos/origen/SET_META', data.meta);
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
            origenes(){
                return this.$store.getters['acarreos/origen/origenes'];
            },
            meta(){
                return this.$store.getters['acarreos/origen/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            origenes: {
                handler(origenes) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = origenes.map((origen, i) => ({
                        index: (i + 1) + self.query.offset,
                        clave: origen.clave_format,
                        tipo: origen.tipo,
                        descripcion: origen.descripcion,
                        fecha: origen.fecha_registro_format,
                        tipo_origen: origen.tipo_origen,
                        estado_tiro: this.getEstado(origen.estado_format, origen.estado_color),
                        buttons: $.extend({}, {
                            id: origen.id,
                           // desactivar: (tiro.estado === 1 && self.$root.can('editar_tiro')) ? true : false,
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
