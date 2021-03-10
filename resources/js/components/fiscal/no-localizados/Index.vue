<template>
    <span>
        <CargaCsv @created="paginate()" />
        <div class="row">
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
    </span>
</template>

<script>
    import CargaCsv from "./CargaCsv";
    export default {
        name: "no-localizados-index",
        components: {CargaCsv},
        data(){
            return{
                HeaderSettings: false,
                columns: [
                    { title: '#', field:'index', tdClass: 'th_index',sortable: false},
                    { title: 'RFC', field: 'rfc', thClass:'th_rfc', thComp: require('../../globals/th-Filter').default, sortable: true},
                    { title: 'Razón Social', field: 'razon_social',thComp: require('../../globals/th-Filter').default, sortable: true},
                    { title: 'Entidad Federativa', thClass:'th.c200', field: 'entidad_federativa',thComp: require('../../globals/th-Filter').default, sortable: true},
                    { title: 'Primera Fecha Publicación', field: 'primera_fecha_publicacion', tdClass: 'td_fecha',  thComp: require('../../globals/th-Date').default, sortable: true},
                ],
                data: [],
                total: 0,
                query: {
                    include: [], scope:['vigente'], sort: 'id',  order: 'desc'
                },
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
            paginate(){
                this.cargando=true;
                return this.$store.dispatch('fiscal/ctg-no-localizado/paginate', {params: this.query})
                    .then(data=>{
                        this.$store.commit('fiscal/ctg-no-localizado/SET_CTG_NO_LOCALIZADOS', data.data);
                        this.$store.commit('fiscal/ctg-no-localizado/SET_META',data.meta)
                    })
                    .finally(()=>{
                        this.cargando=false;
                    })

            },
        },
        computed: {
            no_localizados(){
                return this.$store.getters['fiscal/ctg-no-localizado/ctg_no_localizados'];
            },
            meta(){
                return this.$store.getters['fiscal/ctg-no-localizado/meta']
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            no_localizados: {
                handler(no_localizados) {
                    let self = this
                    self.$data.data = []
                    no_localizados.forEach(function (no_localizado, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            rfc: no_localizado.rfc,
                            razon_social: no_localizado.razon_social,
                            entidad_federativa: no_localizado.entidad_federativa,
                            primera_fecha_publicacion: no_localizado.primera_fecha_publicacion,

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
        },
    }
</script>

<style>

</style>