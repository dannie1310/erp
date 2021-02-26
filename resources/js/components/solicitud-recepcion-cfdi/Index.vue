<template>
    <span>
        <div class="row">
            <div class="col-12">
                <router-link :to="{name: 'soliciitud-recepcion-cfdi-create'}" v-if="$root.can('registrar_solicitud_recepcion_cfdi',true)" class="btn btn-app float-right" :disabled="cargando">
                    <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                    <i class="fa fa-plus" v-else></i>
                    Registrar
                </router-link>
            </div>
        </div>
        <div class="row" v-if="1==0">
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
    </span>
</template>

<script>

    export default {
        name: "cfd-sat-index",

        data() {
            return {
                cargando: false,

                HeaderSettings: false,
                columns: [
                    { title: '#', field:'index',sortable: false},
                    { title: 'Fecha', field: 'fecha', sortable: true},
                ],
                data: [],
                total: 0,
                query: {
                    include: [],
                    sort: 'fecha',  order: 'desc'
                },
                daterange: null,
            }
        },
        mounted(){
            this.$Progress.start();
            this.paginate()
                .finally(() => {
                    this.$Progress.finish();
                })
        },

        methods: {
            paginate(){
                this.cargando=true;
                return this.$store.dispatch('entrega-cfdi/solicitud-recepcion-cfdi/paginate', {params: this.query})
                    .then(data=>{

                    })
                    .finally(()=>{
                        this.cargando=false;
                    })
            },
        },
        computed: {
            solicitudes(){
                return this.$store.getters['entrega-cfdi/solicitud-recepcion-cfdi/solicitudes'];
            },
            meta(){
                return this.$store.getters['entrega-cfdi/solicitud-recepcion-cfdi/meta']
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            solicitudes: {
                handler(cfdi) {
                    let self = this
                    self.$data.data = []
                    cfdi.forEach(function (solicitud, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            fecha: solicitud.fecha,

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
            },
        },
    }
</script>

<style scoped>

</style>
