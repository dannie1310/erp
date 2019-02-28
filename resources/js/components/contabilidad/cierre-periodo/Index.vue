<template>
    <div class="row">
        <div class="col-12">
            <cierre-periodo-create></cierre-periodo-create>
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
    import CierrePeriodoCreate from "./Create";
    export default {
        name: "cierre-periodo-index",
        components: {CierrePeriodoCreate},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'id', sortable: false },
                    { title: 'Año', field: 'anio', sortable: true },
                    { title: 'Mes', field: 'mes', sortable: true },
                    { title: 'Fecha de Cierre', field: 'fecha_cierre', sortable: true },
                    { title: 'Estatus', field: 'estatus', sortable: false },
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons')},
                ],
                data: [],
                total: 0,
                query: {
                },
                estado: ""
            }
        },

        mounted() {
            this.paginate()
        },

        methods: {
            paginate(payload = {}) {
                return this.$store.dispatch('contabilidad/cierre-periodo/paginate', payload)
            }
        },
        computed: {
            cierres(){
                return this.$store.getters['contabilidad/cierre-periodo/cierres'];
            },
            meta(){
                return this.$store.getters['contabilidad/cierre-periodo/meta'];
            },
        },
        watch: {
            cierres: {
                handler(cierres) {
                    let self = this
                    self.$data.data = []
                    cierres.forEach(function (cierre, i) {
                        if(typeof cierre.apertura  !== 'undefined') {
                            self.$data.estado = cierre.apertura.estatus;
                        }else{
                            self.$data.estado = 'Cerrado';
                        }
                        self.$data.data.push({
                            id: (i + 1) + self.query.offset,
                            anio: cierre.anio,
                            mes: cierre.mes,
                            fecha_cierre: cierre.fecha,
                            estatus: self.$data.estado,
                            buttons: $.extend({}, {
                                edit: self.$root.can('editar_cierre_periodo') ? true : undefined,
                                id: cierre.id,
                                datos: cierre
                            })
                        })
                    });
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
                handler (query) {
                    this.paginate(query)
                },
                deep: true
            }
        },
    }
</script>