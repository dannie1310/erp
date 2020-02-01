<template>
    <div class="row">
        <div class="col-12">
            <create @created="paginate()"></create>
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
    export default {
        name: "incidencia-index",
        data(){
            return{
                HeaderSettings: false,
                columns: [
                    { title: '#', field:'index',sortable: false},
                    { title: 'Razón Social', field: 'razon_social',thComp: require('../../globals/th-Filter').default, sortable: true},
                    { title: 'Nombre Corto', field: 'nombre_corto',thComp: require('../../globals/th-Filter').default, sortable: true},
                    { title: 'Descripción Corta', field: 'descripcion_corta',thComp: require('../../globals/th-Filter').default, sortable: true},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {
                    include: 'ctg_banco', sort: 'id_empresa',  order: 'desc'
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
                return this.$store.dispatch('seguridad/control-interno/incidencia/paginate', {params: this.query})
                    .then(data=>{
                        this.$store.commit('seguridad/control-interno/incidencia/SET_INCIDENCIAS', data.data);
                        this.$store.commit('seguridad/control-interno/incidencia/SET_META',data.meta)
                    })
                    .finally(()=>{
                        this.cargando=false;
                    })

            }
        },
        computed: {
            bancos(){
                return this.$store.getters['seguridad/control-interno/incidencia/incidencias'];
            },
            meta(){
                return this.$store.getters['seguridad/control-interno/incidencia/meta']
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            incidencias: {
                handler(incidencias) {
                    let self = this
                    self.$data.data = []
                    incidencias.forEach(function (incidencia, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                           /* razon_social: banco.razon_social,
                            descripcion_corta: banco.ctg_banco?banco.ctg_banco.descripcion_corta:'--',
                            nombre_corto: banco.ctg_banco?banco.ctg_banco.nombre_corto:'--',
                            buttons: $.extend({}, {
                                show: true,
                                edit: true,
                                id: banco.id
                            })*/
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
