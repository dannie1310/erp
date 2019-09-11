<template>
    <div class="row">
        <div class="col-12">
            <create @created="paginate()"></create>
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
    import Create from "./Create";
    export default {
        name: "inventario-fisico-index",
        components: {Create},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Folio', field: 'numero_folio', sortable: true, thComp: require('../../globals/th-Filter')},
                    { title: 'Tipo', field: 'id_tipo', sortable: true, thComp: require('../../globals/th-Filter')},
                    { title: 'Fecha Inicio', field: 'fecha_hora_inicio', sortable: true, thComp: require('../../globals/th-Filter')},
                    { title: 'Usuario Inicio', field: 'usuario_inicia', sortable: true, thComp: require('../../globals/th-Filter')},
                    { title: 'Estatus', field: 'estado', sortable: true},
                ],
                data: [],
                total: 0,
                query: {sort: 'folio', order: 'desc'},
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
                return this.$store.dispatch('almacenes/inventario-fisico/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('almacenes/inventario-fisico/SET_INVETARIOS', data.data);
                        this.$store.commit('almacenes/inventario-fisico/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            }
        },
        computed: {
            inventarios(){
                return this.$store.getters['almacenes/inventario-fisico/inventarios'];
            },
            meta(){
                return this.$store.getters['almacenes/inventario-fisico/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            inventarios: {
                handler(inventarios) {
                    let self = this
                    self.$data.data = []
                    inventarios.forEach(function (inventario, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            numero_folio: inventario.folio,
                            id_tipo: inventario.id_tipo,
                            fecha_hora_inicio: inventario.fecha_hora_inicio,
                            usuario_inicia: inventario.usuario_inicia,
                            estado: inventario.estado,
                            buttons: $.extend({}, {
                                id: inventario.id,
                                estado: inventario.estado,
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