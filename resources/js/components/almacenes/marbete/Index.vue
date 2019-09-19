<template>
    <div class="row">
        <div class="col-12">
<Create></Create>
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
import Create from './Create';
    export default {
        name: "marbete-index",
        // props:['id'],
        components: {Create},
        data () {
            return{
                HeaderSettings: false,
                columns: [
                    { title: '#', field:'index',sortable: false},
                    { title: 'Folio', field: 'folio',  thComp: require('../../globals/th-Filter'), sortable: true},
                    { title: 'Folio Inventario',  thComp: require('../../globals/th-Filter'), field:'id_inventario_fisico', sortable:true},
                    { title: 'Almacén', field:'id_almacen',  thComp: require('../../globals/th-Filter'), sortable: true},
                    { title: 'Material', field:'id_material',  thComp: require('../../globals/th-Filter'), sortable: true},
                    { title: 'Acciones', field: 'buttons', tdComp: require('./partials/ActionButtons')},
                ],
                data: [],
                total: 0,
                query: {

                },
                cargando: false
            }
        },
        mounted(){
            // console.log("id",this.id);
            this.$Progress.start();
            this.paginate()
                .finally(() => {
                    this.$Progress.finish();
                })
        },
        methods: {
            paginate() {
                // scope:'InventarioFisico:'+this.id,
                    this.cargando = true;
                return this.$store.dispatch('almacenes/marbete/paginate', {
                    id: this.id,
                    params:{ include:['almacen','material','inventario_fisico',], order:'desc', sort:'folio'}
                })
                    .then(data => {
                        // console.log(data);
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
                                id: marbete.id
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
