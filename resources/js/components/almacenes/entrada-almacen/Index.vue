<template>
    <div class="row">
        <div class="col-12"  v-if="$root.can('registrar_entrada_almacen')" :disabled="cargando">
            <button @click="create" class="btn btn-app btn-info float-right">
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
        <!-- /.col -->
    </div>
</template>

<script>
    import Create from './Create'
    export default {
        name: "entrada-almacen-index",
        components: {Create},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Folio', field: 'numero_folio', sortable: true, thComp: require('../../globals/th-Filter').default},
                    { title: 'Fecha', field: 'fecha',sortable: true, thComp: require('../../globals/th-Date').default},
                    { title: 'Empresa', field: 'id_empresa',sortable: true, thComp: require('../../globals/th-Filter').default},
                    { title: 'Referencia', field: 'referencia', sortable: true, thComp: require('../../globals/th-Filter').default},
                    { title: 'Observaciones', field: 'observaciones', sortable: true},
                    { title: 'Estatus', field: 'estado', sortable: true},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default}
                ],
                data: [],
                total: 0,
                query: { include: ['empresa','relaciones'], sort: 'numero_folio', order: 'desc'},
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
                return this.$store.dispatch('almacenes/entrada-almacen/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('almacenes/entrada-almacen/SET_ENTRADAS', data.data);
                        this.$store.commit('almacenes/entrada-almacen/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            create() {
                this.$router.push({name: 'entrada-almacen-create'});
            },
        },
        computed: {
            entradas(){
                return this.$store.getters['almacenes/entrada-almacen/entradas'];
            },
            meta(){
                return this.$store.getters['almacenes/entrada-almacen/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            entradas: {
                handler(entradas) {
                    let self = this
                    self.$data.data = []
                    entradas.forEach(function (entrada, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            numero_folio: entrada.numero_folio_format,
                            fecha: entrada.fecha_format,
                            id_empresa: entrada.empresa.razon_social,
                            referencia: entrada.referencia,
                            observaciones: entrada.observaciones,
                            estado: entrada.estado_format,
                            buttons: $.extend({}, {
                                show: true,
                                id: entrada.id,
                                estado: entrada.estado,
                                pagina: self.query.offset,
                                delete: self.$root.can('eliminar_entrada_almacen') ? true : false,
                                relaciones: entrada.relaciones.data,
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
<style>
    .money
    {
        text-align: right;
    }
</style>
