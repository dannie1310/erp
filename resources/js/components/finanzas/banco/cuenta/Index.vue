<template>
    <div class="row">
        <div class="col-12">
            <create v-bind:id="id" @created="find()"></create>
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
                        include:'cuentas.moneda,cuentas.tiposCuentasObra',
                    }
                }).then(data => {
                    this.cuentas = data.cuentas.data;

                }).finally(()=>{
                    this.getData();
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
                        fecha: cuenta.fecha,
                        saldo: cuenta.saldo,
                        moneda: cuenta.moneda.nombre + ' (' + cuenta.moneda.abreviatura + ')',
                        chequera: cuenta.chequera === 0?'N':'S',
                        tipo: cuenta.tiposCuentasObra.descripcion,
                        abreviatura: cuenta.abreviatura,
                        buttons: $.extend({}, {
                            show: true,
                            id: cuenta.id
                        })
                    })
                })

            }

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
