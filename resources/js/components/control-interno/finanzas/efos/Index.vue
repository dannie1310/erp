<template>
    <div class="row">
        <div class="col-12">
            <Layout @change="paginate()"></Layout>
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
    import Layout from  "./CargarLayout";
    export default {
        name: "lista-efos-index",
        components: {Layout},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'RFC', field: 'rfc', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Razón Social', field: 'razon_social', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Fecha Presunto', field: 'fecha_presunto', sortable: false},
                    { title: 'Fecha Definitivo', field: 'fecha_definitivo', sortable: false},
                    { title: 'Fecha Sentencia Favorable', field: 'fecha_sentencia_favorable', sortable: false},
                    { title: 'Fecha Desvirtuado', field: 'fecha_desvirtuado', sortable: false},
                    { title: 'Estado', field: 'estado', sortable: false}
                ],
                data: [],
                total: 0,
                query: {include: 'estado'},
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
                return this.$store.dispatch('seguridad/finanzas/ctg-efos/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('seguridad/finanzas/ctg-efos/SET_EFOS', data.data);
                        this.$store.commit('seguridad/finanzas/ctg-efos/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
        },
        computed: {
            efos(){
                return this.$store.getters['seguridad/finanzas/ctg-efos/efos'];
            },
            meta(){
                return this.$store.getters['seguridad/finanzas/ctg-efos/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            efos: {
                handler(famls) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = famls.map((efo, i) => ({
                        index: (i + 1) + self.query.offset,
                        rfc: efo.rfc,
                        razon_social: efo.razon_social,
                        fecha_presunto: efo.fecha_presunto,
                        fecha_definitivo: efo.fecha_definitivo,
                        fecha_sentencia_favorable: efo.fecha_sentencia_favorable,
                        fecha_desvirtuado: efo.fecha_desvirtuado,
                        estado: efo.estado.descripcion
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
