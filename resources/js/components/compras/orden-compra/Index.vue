<template>
    <div class="row">
        <div class="col-md-12">
            <Registro @created="paginate()"></Registro>
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
    import Registro from "./partials/Registrar";
    export default {
        name: "orden-compra-index",
        components:{Registro},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Folio', field: 'numero_folio', thComp: require('../../globals/th-Filter').default, sortable: true },
                    { title: 'Folio SAO Solicitud', field: 'id_antecedente', thComp: require('../../globals/th-Filter').default, sortable: false },
                    { title: 'Empresa', field: 'id_empresa', thComp: require('../../globals/th-Filter').default, sortable: true },
                    { title: 'Fecha de Registro', field: 'FechaHoraRegistro', sortable: true },
                    { title: 'Observaciones', field: 'observaciones', thComp: require('../../globals/th-Filter').default, sortable: true },
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {scope: 'areasCompradorasAsignadas', include: ['solicitud','empresa'], sort: 'id_transaccion', order: 'desc'},
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
                return this.$store.dispatch('compras/orden-compra/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('compras/orden-compra/SET_ORDENES', data.data);
                        this.$store.commit('compras/orden-compra/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            }
        },
        computed: {
            ordenes(){
                return this.$store.getters['compras/orden-compra/ordenes'];
            },
            meta(){
                return this.$store.getters['compras/orden-compra/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            ordenes: {
                handler(ordenes) {
                    let self = this
                    self.$data.data = []
                    ordenes.forEach(function (orden, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            numero_folio: orden.numero_folio_format,
                            FechaHoraRegistro: orden.fecha_format,
                            observaciones: orden.observaciones_format,
                            id_empresa: orden.empresa.razon_social,
                            id_antecedente: orden.solicitud.numero_folio_format,
                            buttons: $.extend({}, {
                                show: true,
                                pdf: self.$root.can('consultar_orden_compra') ? true : false,
                                id: orden.id,
                                tiene_entradas: orden.entradas_almacen,
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

<style scoped>

</style>
