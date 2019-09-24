<template>
    <div class="row">
        <div class="col-12">
            <Create @created="paginate()"></Create>
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
        name: "conteo-index",
        components: {Create},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field:'index',sortable: false},
                    { title: 'Folio Inventario', field:'id_inventario_fisico', sortable:true,  thComp: require('../../globals/th-Filter')},
                    { title: 'Folio', field: 'folio', sortable: true,  thComp: require('../../globals/th-Filter')},
                    { title: 'Almacén', field:'id_almacen', sortable: true,  thComp: require('../../globals/th-Filter')},
                    { title: 'Material', field:'id_material', sortable: true,  thComp: require('../../globals/th-Filter')},
                    { title: 'Acciones', field: 'buttons', tdComp: require('./partials/ActionButtons')},
                ],
                data: [],
                total: 0,
                query: {include:['almacen','material','inventario_fisico'], sort:'id', order:'desc'},
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
                return this.$store.dispatch('almacenes/marbete/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('almacenes/marbete/SET_MARBETES', data.data);
                        this.$store.commit('almacenes/marbete/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            }
        },
        computed: {
            marbetes(){
                return this.$store.getters['almacenes/marbete/marbetes'];
            },
            meta(){
                return this.$store.getters['almacenes/marbete/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            marbetes: {
                handler(marbetes) {
                    let self = this
                    self.$data.data = []
                    marbetes.forEach(function (marbete, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            folio: marbete.folio_format,
                            id_inventario_fisico: marbete.inventario_fisico.folio_format,
                            id_almacen: marbete.almacen.descripcion,
                            id_material: marbete.material.descripcion,
                            buttons: $.extend({}, {
                                id: marbete.id,
                                id_inventario_fisico: marbete.id_inventario_fisico,
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
