<template>
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
</template>

<script>
    export default {
        name: "pago-manual-index",
        components: {},
        data(){
            return{
                HeaderSettings: false,
                columns: [
                    { title: '#', field:'index',sortable: false},
                    { title: 'Tipo', field: 'id_tipo', thComp: require('../../globals/th-Filter').default, sortable: true },
                    { title: 'Descripción', field: 'descripcion', thComp: require('../../globals/th-Filter').default, sortable:true},
                    { title: 'Responsable', field:'nombre', thComp: require('../../globals/th-Filter').default, sortable:true},
                    { title: 'Saldo',field: 'saldo', tdClass: 'td_money', thClass: 'th_money', sortable: true},
                    { title: 'Fecha',field:'fecha',sortable:true},
                    // { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {
                    include: 'tipo_fondo', scope:'ConResponsable', sort: 'id_fondo',  order: 'desc'

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
                return this.$store.dispatch('cadeco/fondo/paginate', {params: this.query})
                    .then(data=>{
                        this.$store.commit('cadeco/fondo/SET_FONDOS', data.data);
                        this.$store.commit('cadeco/fondo/SET_META',data.meta)
                    })
                    .finally(()=>{
                        this.cargando=false;
                    })

            }
        },
        computed: {
            fondos(){
              return this.$store.getters['cadeco/fondo/fondos'];
            },
            meta(){
              return this.$store.getters['cadeco/fondo/meta']
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            fondos: {
                handler(fondos) {
                    let self = this
                    self.$data.data = []
                    fondos.forEach(function (fondo, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            id_tipo: fondo.tipo_fondo.descripcion,
                            nombre: fondo.nombre.toUpperCase(),
                            fecha: fondo.fecha_format,
                            saldo: `$ ${parseFloat(fondo.saldo).formatMoney(2)}`,
                            descripcion: fondo.descripcion.toUpperCase(),
                            buttons: $.extend({}, {
                                show: true,
                                id: fondo.id
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