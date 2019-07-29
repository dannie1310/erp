<template>
    <div class="row">
        <div class="col-12"  v-if="" :disabled="cargando">
            <fondo-create></fondo-create>
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
    import FondoCreate from "./Create";
    export default {
        name: "fondo-index",
        components: {FondoCreate},
        data(){
            return{
                HeaderSettings: false,
                columns: [
                    { title: '#', field:'index',sortable: false},
                    { title: 'Tipo', field: 'id_tipo', sortable: true },
                    { title: 'Descripción', field: 'descripcion', sortable:true},
                    { title: 'Responsable', field:'nombre',sortable:true},
                    { title: 'Saldo',field: 'saldo', tdClass: 'money', thClass: 'th_money', sortable: false  },
                    { title: 'Fecha',field:'fecha',sortable:true },
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons')},
                ],
                data: [],
                total: 0,
                query: {
                    include: 'tipo_fondo', scope:'ConResponsable'
                },
                cargando: false

            }
        },
        mounted() {
          //this.query.scope = 'conResponsable';
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
                handler(fondos){
                   // console.log("here "+fondos);
                    let self = this
                    self.$data.data = []
                      fondos.forEach(function(fondo, i){
                          console.log(fondo);
                          self.$data.data.push({
                            index: (i+1)+self.query.offset,
                            tipo: fondo.id_tipo,
                            saldo: `$ ${parseFloat(fondo.saldo).formatMoney(2)}`,
                            descripcion: fondo.descripcion,
                        })

                    });

                },
                deep: true
            }
        },
        meta: {
            handler(meta){
                let total = meta.pagination.total
                this.$data.local = total
            },
            deep: true
        },
        query: {
            handler( query ){
                this.paginate(query)
            },
            deep:true
        },
        cargando(val){
            $('tbody').css({
                  '-webkit-filter': val ? 'blur(2px)' : '',
                  'pointer-events': val ? 'none' : ''
                              });
        }
    }
</script>
<style scoped>

</style>

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