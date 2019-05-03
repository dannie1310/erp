<template>
    <div class="row">
        <div class="col-12">
            <create @created="paginate()"></create>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Buscar" v-model="search">
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
        <!-- /.col -->
    </div>
</template>

<script>
    import Create from "./Create";
    export default {
        name: "cuenta-almacen-index",
        components: {Create},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Cuenta', field: 'cuentas_almacenes__cuenta', sortable: true },
                    { title: 'Almacén', field: 'id_almacen', sortable: true },
                    { title: 'Tipo de Almacén', field: 'almacen__tipo_almacen', sortable: true },
                    { title: 'Editar', field: 'buttons',  tdComp: require('./partials/ActionButtons')},
                ],
                data: [],
                total: 0,
                query: {},
                search: '',
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
                    return this.$store.dispatch('contabilidad/cuenta-almacen/paginate', { params: this.query })
                        .then(data => {
                            this.$store.commit('contabilidad/cuenta-almacen/SET_CUENTAS', data.data);
                            this.$store.commit('contabilidad/cuenta-almacen/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            }
        },

        computed: {
            cuentas(){
                return this.$store.getters['contabilidad/cuenta-almacen/cuentas'];
            },
            meta(){
                return this.$store.getters['contabilidad/cuenta-almacen/meta'];
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
                        index: (i + 1) + self.query.offset,
                        id_almacen: cuenta.almacen.descripcion,
                        almacen__tipo_almacen: cuenta.almacen.tipo,
                        cuentas_almacenes__cuenta: cuenta.cuenta,
                        buttons: $.extend({}, {
                            edit: self.$root.can('editar_cuenta_almacen') ? true : undefined,
                            id: cuenta.id,
                            cuenta: cuenta
                        })
                    }));
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
        }
    }
</script>