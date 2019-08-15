<template>
    <div class="row">
        <div class="col-12">
            <create v-bind:id="id" @created="paginate()"></create>
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
    import Create from './Create';
    export default {
        name: "cuenta-index",
        props:['id'],
        components: {Create},
        data() {
            return{
                HeaderSettings: false,
                columns: [
                    { title: 'Número', field:'cuenta',sortable: false},
                    { title: 'Apertura', field: 'fecha', sortable: false},
                    { title: 'Saldo Inicial', field:'saldo',tdClass: 'money', thClass: 'th_money'},
                    { title: 'Moneda', field:'moneda', sortable: false},
                    { title: 'Chequera', field:'chequera', sortable: false},
                    { title: 'Tipo', field:'tipo', sortable: false},
                    { title: 'Abreviatura', field:'abreviatura', sortable: false},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons')}

                ],
                data: [],
                total: 0,
                query: {
                    id:this.id,
                    sort: 'id_cuenta',
                    order: 'DESC',
                    include:'moneda,tiposCuentasObra'
                },
                cargando: false,

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
                return this.$store.dispatch('cadeco/cuenta/paginate', {params: this.query})
                    .then(data => {
                        this.$store.commit('cadeco/cuenta/SET_CUENTAS', data.data);
                        this.$store.commit('cadeco/cuenta/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
        },
        computed: {
            cuentas(){
                return this.$store.getters['cadeco/cuenta/cuentas'];
            },
            meta(){
                return this.$store.getters['cadeco/cuenta/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            cuentas: {
                handler(cuentas) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = cuentas.map((cuenta, i) => ({
                        index: (i + 1),
                        cuenta: cuenta.numero,
                        fecha: cuenta.fecha,
                        saldo: cuenta.saldo,
                        moneda: cuenta.moneda.nombre + ' (' + cuenta.moneda.abreviatura + ')',
                        chequera: cuenta.chequera === 0?'N':'S',
                        tipo: cuenta.tiposCuentasObra?cuenta.tiposCuentasObra.descripcion:'---',
                        abreviatura: cuenta.abreviatura,
                        buttons: $.extend({}, {
                            show: self.$root.can('consultar_cuenta_corriente') ? true : false,
                            edit: self.$root.can('editar_cuenta_corriente') ? true : false,
                            id: cuenta.id
                        })
                    }));
                    // let total =this.$data.data.length;
                    // this.$data.total = total
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
                handler () {
                    this.paginate()
                },
                deep: true
            },

        },


    }
</script>

<style>
    .money
    {
        text-align: right;
    }
    .th_money
    {
        width: 150px;
        max-width: 150px;
        min-width: 100px;
    }
</style>
