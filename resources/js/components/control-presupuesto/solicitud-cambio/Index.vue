<template>
  <div class="col-12">
      <div class="card">
          <div class="card-header">
              <div class="row">
                  <div class="col">
                      <div class="form-group">
                          <!-- <input type="text" class="form-control" placeholder="Buscar" v-model="search"> -->
                      </div>
                  </div>
              </div>
          </div>
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
</template>

<script>
export default {
  name: "solicitud-cambio-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: 'Número de Folio', field: 'numero_folio', thComp: require('../../globals/th-Filter').default,  sortable: true},
                    { title: 'Tipo Orden', field: 'tipo_orden', sortable: true },
                    { title: 'Fecha Solicitud', field: 'fecha',  sortable: true  },
                    { title: 'Usuario Solicita', field: 'usuario', sortable: true  },
                    { title: 'Motivo', field: 'motivo',sortable: true },
                    { title: 'Importe Afectación', field: 'monto_afectacion', sortable: false },
                    { title: 'Estatus', field: 'estatus', sortable: false },
                    // { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {},
                search: '',
                cargando: false
            }
        },
        mounted() {
            // this.query.include = 'subcontrato.empresa';
            // this.query.sort = 'numero_folio';
            // this.query.order = 'DESC';
            this.lista_solicitudes();
            this.$Progress.start();
            this.paginate()
                .finally(() => {
                    this.$Progress.finish();
                })
        },
        methods: {
          paginate() {
                this.cargando = true;
                return this.$store.dispatch('control-presupuesto/solicitud-cambio/paginate', {
                    params: this.query
                })
                    .then(data => {
                        this.$store.commit('control-presupuesto/solicitud-cambio/SET_SOLICiTUDES', data.data);
                        this.$store.commit('control-presupuesto/solicitud-cambio/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            lista_solicitudes(){
                
            },
        },
        computed: {
          solicitudes(){
                return this.$store.getters['control-presupuesto/solicitud-cambio/solicitudesCambio'];
            },
            meta(){
                return this.$store.getters['control-presupuesto/solicitud-cambio/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {}
}
</script>

<style>

</style>