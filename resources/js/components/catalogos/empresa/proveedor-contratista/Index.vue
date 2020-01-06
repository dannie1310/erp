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
    import Create from "./Create";
    export default {
        name: "proveedor-contratista-index",
        components: {Create},
        data(){
            return{
                HeaderSettings: false,
                columns: [
                    { title: '#', field:'index',sortable: false},
                    { title: 'R.F.C.', field: 'rfc',thComp: require('../../../globals/th-Filter').default, sortable: true},
                    { title: 'Razón Social', field: 'razon_social',thComp: require('../../../globals/th-Filter').default, sortable: true},
                    { title: 'Tipo', field: 'tipo_empresa', sortable: true},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},


                ],
                data: [],
                total: 0,
                query: {
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
                return this.$store.dispatch('cadeco/proveedor-contratista/paginate', {params: this.query})
                    .then(data=>{
                        this.$store.commit('cadeco/proveedor-contratista/SET_PROVEEDOR_CONTRATISTAS', data.data);
                        this.$store.commit('cadeco/proveedor-contratista/SET_META',data.meta)
                    })
                    .finally(()=>{
                        this.cargando=false;
                    })

            },
            create() {
                this.$router.push({name: 'proveedor-contratista-create'});
            },
        },
        computed: {
            proveedorContratistas(){
                
                return this.$store.getters['cadeco/proveedor-contratista/proveedorContratistas'];
            },
            meta(){
                return this.$store.getters['cadeco/proveedor-contratista/meta']
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            proveedorContratistas: {
                handler(proveedorContratistas) {
                    let self = this
                    self.$data.data = []
                    proveedorContratistas.forEach(function (proveedorContratista, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            rfc: proveedorContratista.rfc,
                            razon_social: proveedorContratista.razon_social,
                            tipo_empresa: proveedorContratista.tipo,
                            buttons: $.extend({}, {
                                id: proveedorContratista.id
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
