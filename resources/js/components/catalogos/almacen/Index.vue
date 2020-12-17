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
        name: "almacen-index",
        components: {Create},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Descripción', field: 'descripcion', sortable: true },
                    { title: 'Tipo', field: 'tipo',  sortable: true },
                    { title: 'Registro', field: 'id_usuario', sortable: true },
                    { title: 'Fecha de Registro', field: 'fecha_registro', sortable: true },
                    { title: 'Acciones', field: 'buttons', thClass:'th_c100', tdClass:'center', tdComp: require('./partials/ActionButtons').default}
                ],
                data: [],
                total: 0,
                query: {sort: 'id_almacen', order: 'desc'},
                tipo: "",
                cargando: false
            }
        },

        mounted() {
            var scope = ''
            if(this.$root.can('consultar_almacen_material'))
            {
               scope += '0';
            }
            if(this.$root.can('consultar_almacen_maquinaria'))
            {
                scope += scope != '' ? ',1' : '1'
            }
            if(this.$root.can('consultar_almacen_maquina_controladora_insumo'))
            {
                scope += scope != '' ? ',2' : '2'
            }
            if(this.$root.can('consultar_almacen_mano_obra'))
            {
                scope += scope != '' ? ',3' : '3'
            }
            if(this.$root.can('consultar_almacen_servicio'))
            {
                scope += scope != '' ? ',4' : '4'
            }
            if(this.$root.can('consultar_almacen_herramienta'))
            {
                scope += scope != '' ? ',5' : '5'
            }
            this.query.scope = "tipo:"+scope;
            this.tipo = scope
            this.$Progress.start();
            this.paginate()
                .finally(() => {
                    this.$Progress.finish();
                })
        },
        methods: {
            paginate() {
                this.cargando = true;
                return this.$store.dispatch('cadeco/almacen/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('cadeco/almacen/SET_ALMACENES', data.data);
                        this.$store.commit('cadeco/almacen/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
        },
        computed: {
            almacenes(){
                return this.$store.getters['cadeco/almacen/almacenes'];
            },
            meta(){
                return this.$store.getters['cadeco/almacen/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            almacenes: {
                handler(almacenes) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = almacenes.map((almacen, i) => ({
                        index : (i + 1) + self.query.offset,
                        descripcion : almacen.descripcion,
                        tipo : almacen.tipo,
                        id_usuario : almacen.registro,
                        fecha_registro : almacen.fecha_registro,
                        buttons: $.extend({}, {
                            edit: almacen.permiso_editar,
                            id: almacen.id
                        })
                    }));
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

<style scoped>

</style>
