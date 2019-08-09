<template>
    <div class="row">
        <div class="col-12">
            <!--            <create ></create>-->
        </div>
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <!--                        <datatable v-bind="$data" />-->
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
        name: "gestion-banco-cuenta-index",
        props:['id'],
        data() {
            return{
                HeaderSettings: false,
                columns: [
                    { title: 'numero', field:'cuenta',sortable: false},
                    { title: 'Apertura', field: 'fecha', sortable: false},
                    { title: 'Saldo Inicial', field:'saldo', sortable: false},
                    { title: 'Moneda', field:'moneda', sortable: false},
                    { title: 'Chequera', field:'chequera', sortable: false},
                    { title: 'Tipo', field:'tipo', sortable: false},
                    { title: 'Abreviatura', field:'abreviatura', sortable: false},

                ],
                data: [],
                total: 0,
                query: {
                    scope:'Bancos', sort: 'id_empresa',  order: 'desc', id:this.id, include:['cuentas']
                },
                cargando: false,
                cuentas:[]

            }
        },
        mounted() {
            // console.log(this.query);
            this.$Progress.start();
            this.find();
            this.getCuentas()
                .finally(() => {
                    this.$Progress.finish();
                })
        },
        methods: {
            find() {
                this.cargando = true;
                this.$store.commit('cadeco/empresa/SET_EMPRESA', null);
                return this.$store.dispatch('cadeco/empresa/find', {
                    id: this.id,
                    scope:'bancos',
                    include:['cuentas']
                }).then(data => {
                    this.$store.commit('cadeco/empresa/SET_EMPRESA', data);
                    // $(this.$refs.modal).modal('show')
                })
            },
            getCuentas() {
                this.cargando = true;
                let self = this
                return self.$store.dispatch('cadeco/empresa/find', {
                    id:self.id,
                    params: {
                        scope: 'bancos',
                        sort: 'id_empresa',
                        order: 'DESC',
                        include:['cuentas']
                    }
                })
                    .then(data => {
                        this.cuentas = data.cuentas.data;
                    })
                    .finally(() => {
                        this.cargando = false;
                    });
            },
        },
        computed: {
            empresa() {
                return this.$store.getters['cadeco/empresa/currentEmpresa']
            },
            empresas(){
                return this.$store.getters['cadeco/empresa/empresas'];
            },
            meta(){
                return this.$store.getters['cadeco/empresa/meta']
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            // empresas: {
                // handler(empresas) {
                //     let self = this
                //     self.$data.data = []
                //     empresas.forEach(function (empresa, i) {
                //         self.$data.data.push({
                //             index: (i + 1) + self.query.offset,
                //             cuenta: empresa.descripcion,
                //             fecha: empresa.direccion,
                //             saldo: empresa.direccion,
                //             moneda: empresa.direccion,
                //             chequera: empresa.direccion,
                //             tipo: empresa.direccion,
                //             abreviatura: empresa.direccion,
                //             // buttons: $.extend({}, {
                //             //     show: true,
                //             //     id: sucursal.id
                //             // })
                //         })
                //
                //     });
                //
                // },
                // deep: true
            // },

        },
    }
</script>

<style scoped>

</style>
