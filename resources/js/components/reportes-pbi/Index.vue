<template>
    <span>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <datatable v-bind="$data" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "reportes-pbi-index",
        data(){
            return{
                HeaderSettings: false,
                columns: [
                    { title: '#', field:'index', thClass:'th_index',sortable: false},
                    { title: 'Nombre', field: 'nombre',thComp: require('../globals/th-Filter').default, sortable: true},
                    { title: 'Descripción', field: 'descripcion',thComp: require('../globals/th-Filter').default, sortable: true},
                    { title: '', field: 'buttons', thClass:'th_index', tdComp: require('./partials/ActionButtons').default}
                ],
                data: [],
                total: 0,
                query: {
                    sort: 'nombre',  order: 'desc'
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
                return this.$store.dispatch('reportes/reporte/paginate', {params: this.query})
                    .then(data=>{
                        this.$store.commit('reportes/reporte/SET_REPORTES', data.data);
                        this.$store.commit('reportes/reporte/SET_META',data.meta)
                    })
                    .finally(()=>{
                        this.cargando=false;
                    })
            },
        },
        computed: {
            reportes(){
                return this.$store.getters['reportes/reporte/reportes'];
            },
            meta(){
                return this.$store.getters['reportes/reporte/meta']
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            reportes: {
                handler(reportes) {
                    let self = this
                    self.$data.data = []
                    reportes.forEach(function (reporte, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            nombre: reporte.nombre,
                            descripcion: reporte.descripcion,
                            buttons: $.extend({}, {
                                id: reporte.id,
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
        },
    }
</script>