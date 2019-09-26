<template>
   <div class="row">
      <div class="col-12">
         <create @created="paginate()"></create>
         <Layout @change="paginate()"></Layout>
         <CodigoBarra @change="paginate()"></CodigoBarra>
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
   import CodigoBarra from "./CodigoBarra";
   import Create from "./Create";
   import Layout from "./CargarLayout";
   export default {
        name: "conteo-index",
       components: {CodigoBarra, Layout, Create},
       data() {
          return {
             HeaderSettings: false,
             columns: [
                { title: '#', field: 'index', sortable: false },
                { title: 'Inventario Físico', field: 'inventario_fisico', thComp: require('../../globals/th-Filter')},
                { title: 'Folio', field: 'id_marbete', sortable: true},
                { title: 'Tipo Conteo', field: 'tipo_conteo', sortable: true, thComp: require('../../globals/th-Filter')},
                { title: 'Cantidad Usados', field: 'cantidad_usados', sortable: true},
                { title: 'Cantidad Nuevos', field: 'cantidad_nuevo', sortable: true},
                { title: 'Cantidad inservibles', field: 'cantidad_inservible', sortable: true},
                { title: 'Total', field: 'total', sortable: true},
                { title: 'Iniciales', field: 'iniciales', sortable: true, thComp: require('../../globals/th-Filter')},
                { title: 'Observaciones', field: 'observaciones'},
                { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons')}
             ],
             data: [],
             total: 0,
             query: {include: ['marbete.inventario_fisico'],sort:'id_marbete', order: 'desc'},
             estado: "",
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
          paginate() {
             this.cargando = true;
             return this.$store.dispatch('almacenes/conteo/paginate', { params: this.query})
                     .then(data => {
                        this.$store.commit('almacenes/conteo/SET_CONTEOS', data.data);
                        this.$store.commit('almacenes/conteo/SET_META', data.meta);
                     })
                     .finally(() => {
                        this.cargando = false;
                     })
          }
       },
       computed: {
          conteos(){
             return this.$store.getters['almacenes/conteo/conteos'];
          },
          meta(){
             return this.$store.getters['almacenes/conteo/meta'];
          },
          tbodyStyle() {
             return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
          }
       },
       watch: {
          conteos: {
             handler(conteos) {
                let self = this
                self.$data.data = []
                conteos.forEach(function (conteo, i) {
                   self.$data.data.push({
                     index: (i + 1) + self.query.offset,
                     id : conteo.id,
                     tipo_conteo : conteo.tipo_conteo_format,
                     id_marbete : conteo.folio_marbete,
                     cantidad_usados : conteo.cantidad_usados ? conteo.cantidad_usados:'-',
                     cantidad_nuevo : conteo.cantidad_nuevo,
                     cantidad_inservible : conteo.cantidad_inservible ? conteo.cantidad_inservible:'-',
                     inventario_fisico : conteo.marbete.inventario_fisico.folio_format,
                     total : conteo.total,
                     iniciales : conteo.iniciales ? conteo.iniciales:'-',
                     observaciones : conteo.observaciones ? conteo.observaciones:'-',
                     buttons: $.extend({}, {
                        id:conteo.id,
                        pagina: self.query.offset,
                        cancelar:true,
                        estado:conteo.marbete.inventario_fisico.estado
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
          cargando(val) {
             $('tbody').css({
                '-webkit-filter': val ? 'blur(2px)' : '',
                'pointer-events': val ? 'none' : ''
             });
          }
       }
    }
</script>
<style>
    .money
    {
        text-align: right;
    }
</style>
