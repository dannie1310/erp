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
                    { title: 'Tipo' },
                    { title: 'Descripción'},
                    { title: 'Responsable'},
                    { title: 'Saldo' },
                    { title: 'Fecha' },
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons')},
                ],
                data: [],
                total:0,
                cargando: false

            }
        },
        mounted() {
            this.$Progress.start();
            // this.paginate()
            //     .finally(() => {
            //         this.$Progress.finish();
            //     })
        },
        methods: {
            // paginate(){
            //    // this.cargando=true;
            //     return this.$store.dispatch('finanzas/fondo/paginate', {params: this.query})
            //         .then(data=>{
            //             this.$store.commit('finanzas/fondo/SET_FONDOS', data.data);
            //             this.$store.commit('finanazas/SET_META',data.meta)
            //         })
            //         .finally(()=>{
            //             this.cargando=false;
            //         })
            //
            // }
        },
        computed: {
            // fondos(){
            //   return this.$store.getters['finanzas/fondo'/fondos];
            // },
            // meta(){
            //   return this.$store.getters['finanzas/fondo/meta']
            // },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            // fondos: {
            //     handler(fondos){
            //         let self = this
            //         self.$data.$data = []
            //         self.$data.data = fondos.map((fondo,i)=>({
            //         index: (i+1)+self.query.offset,
            //
            //         }));
            //     },
            //     deep: true
            // }
        },
        // meta: {
        //     handler(meta){
        //         let total = meta.pagination.total
        //         this.$data.local = total
        //     },
        //     deep: true
        // },
        // query: {
        //     handler( query ){
        //         this.paginate(query)
        //     },
        //     deep:true
        // },
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