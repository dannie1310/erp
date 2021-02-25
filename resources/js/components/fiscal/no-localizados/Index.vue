<template>
    <span>
        <!-- <create @created="paginate()"></create> -->
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
    // import Create from "./Create";
    export default {
        name: "no-localizados-index",
        // components: {Create},
        data(){
            return{
                HeaderSettings: false,
                columns: [
                    { title: '#', field:'index', tdClass: 'th_index',sortable: false},
                    { title: 'RFC', field: 'rfc', thClass:'th_rfc', thComp: require('../../globals/th-Filter').default, sortable: true},
                    { title: 'Razón Social', field: 'razon_social',thComp: require('../../globals/th-Filter').default, sortable: true},
                    { title: 'Entidad Federativa', thClass:'th.c200', field: 'entidad_federativa',thComp: require('../../globals/th-Filter').default, sortable: true},
                    { title: 'Fecha Última Publicación', field: 'fecha_primera_publicacion', tdClass: 'td_fecha', thComp: require('../../globals/th-Filter').default, sortable: true},

                    // { title: 'Fecha', field: 'fecha', thComp: require('../../globals/th-Date').default, sortable: true},
                    // { title: 'Importe', field: 'monto', sortable: true},
                    // { title: 'A Cta.', field: 'a_cuenta', sortable: false},
                    // { title: 'Saldo', field: 'saldo', sortable: true},
                    // { title: 'Estado', field: 'estado',thComp: require('../../globals/th-Filter').default, sortable: true},
                    // { title: 'Tipo', field: 'opciones',thComp: require('../../globals/th-Filter').default, sortable: true},
                    // { title: 'Observaciones Contrarecibo', field: 'observaciones',thComp: require('../../globals/th-Filter').default, sortable: false},
                    // { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default}
                ],
                data: [],
                total: 0,
                query: {
                    include: ['ctg_no_localizados_registro'], scope:['vigente'], sort: 'id_transaccion',  order: 'desc'
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
                return this.$store.dispatch('fiscal/no-localizados/paginate', {params: this.query})
                    .then(data=>{
                        this.$store.commit('fiscal/no-localizados/SET_NO_LOCALIZADOS', data.data);
                        this.$store.commit('fiscal/no-localizados/SET_META',data.meta)
                    })
                    .finally(()=>{
                        this.cargando=false;
                    })

            },
            create() {
                // this.$router.push({name: 'factura-create'});
            },
        },
        computed: {
            no_localizados(){
                return this.$store.getters['fiscal/no-localizados/no_localizados'];
            },
            meta(){
                return this.$store.getters['finanzas/factura/meta']
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
                            entidad_federativa: no_localizado.ctg_no_localizados_registro.entidad_federativa,
                            fecha_primera_publicacion: no_localizado.ctg_no_localizados_registro.fecha_primera_publicacion,

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