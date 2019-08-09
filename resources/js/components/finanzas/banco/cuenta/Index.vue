<template>
    <div class="row">
        <div class="col-12">
            <create v-bind:id="id"></create>
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
                cuentas:[],

            }
        },
        mounted() {
            this.$Progress.start();

            this.find()
                .finally(() => {
                    this.getData();
                    this.$Progress.finish();
                })
        },
        methods: {
            find() {
                this.cargando = true;
                this.$store.commit('cadeco/empresa/SET_EMPRESA', null);
                return this.$store.dispatch('cadeco/empresa/find', {
                    id:this.id,
                    params: {
                        scope: 'bancos',
                        sort: 'id_empresa',
                        order: 'DESC',
                        include:['cuentas']
                    }
                }).then(data => {
                    this.cuentas = data.cuentas.data;

                }).finally(()=>{
                    this.cargando=false;
                })
            },
            getData(){
                let self = this
                self.$data.data = []
                this.cuentas.forEach(function (cuenta, i) {
                    self.$data.data.push({
                        index: (i + 1),
                        cuenta: cuenta.numero,
                        saldo: cuenta.numero,
                        moneda: cuenta.numero,
                        chequera: cuenta.numero,
                        tipo: cuenta.numero,
                        abreviatura: cuenta.numero,
                        // buttons: $.extend({}, {
                        //     show: true,
                        //     id: sucursal.id
                        // })
                    })
                })

            }

        },
    }
</script>

<style scoped>

</style>
