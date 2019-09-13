<template>
   <div class="row">
      <div class="col-12">
         <Layout @created="paginate()"></Layout>
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
   import Layout from "./cargar-layout";
    export default {
        name: "conteo-index",
       components: {Layout},
       data() {
          return {
             HeaderSettings: false,
             columns: [
                { title: '#', field: 'index', sortable: false },
                { title: 'Folio', field: 'numero_folio', sortable: true, thComp: require('../../globals/th-Filter')},
                { title: 'Usuario Inició', field: 'usuario_inicia', sortable: true, thComp: require('../../globals/th-Filter')},
                { title: 'Fecha/Hora Inicio', field: 'fecha_hora_inicio', sortable: true, thComp: require('../../globals/th-Filter')},
                { title: 'Cantidad de Marbetes', field: 'cantidad_marbetes', sortable: true, thComp: require('../../globals/th-Filter')},
                { title: 'Estado', field: 'estado', sortable: true},
                // { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons')},
             ],
             data: [],
             total: 0,
             query: {include: ['usuario'],sort: 'folio', order: 'desc'},
             estado: "",
             cargando: false
          }
       },
    }
</script>
<style>
    .money
    {
        text-align: right;
    }
</style>
