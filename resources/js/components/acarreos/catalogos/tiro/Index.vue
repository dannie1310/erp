<template>
    <div class="row">
        <!--<div class="col-12">
            <create @created="paginate()"></create>
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
        name: "tiro-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', thClass: 'th_index', tdClass: 'td_index', sortable: false },
                    { title: 'Clave', field: 'clave',sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Descripción', field: 'descripcion', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Fecha Registro', field: 'fecha', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Estado', field: 'estatus', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Concepto', field: 'descripcion', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    // { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default}
                ],
                data: [],
                total: 0,
                query: {scope:'', sort: 'IdTiro', order: 'asc'},
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
            }
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
                    tiros.forEach(function (tiro, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            clave: tiro.clave_format,
                            descripcion: tiro.descripcion,
                            fecha: tiro.fecha_registro_format,
                            estatus: tiro.estado_format,
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

<style scoped>

</style>
