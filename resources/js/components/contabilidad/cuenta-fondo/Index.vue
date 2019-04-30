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
        name: "cuenta-fondo-index",
        components: {Create},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Cuenta', field: 'cuenta__cuenta', sortable: true },
                    { title: 'Fondo', field: 'id_fondo', sortable: true },
                    { title: 'Saldo', field: 'saldo', sortable: true },
                    { title: 'Editar', field: 'buttons', tdComp: require('./partials/ActionButtons')},
                ],
                data: [],
                total: 0,
                query: {
                    include: 'cuentaFondo',
                    scope: 'conCuenta'
                },
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
                return this.$store.dispatch('cadeco/fondo/paginate', { params: this.query })
                    .then(data => {
                        this.$store.commit('cadeco/fondo/SET_FONDOS', data.data);
                        this.$store.commit('cadeco/fondo/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            }
        },

        computed: {
            fondos(){
                return this.$store.getters['cadeco/fondo/fondos'];
            },
            meta(){
                return this.$store.getters['cadeco/fondo/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },

        watch: {
            fondos: {
                handler(fondos) {
                    let self = this;
                    self.$data.data = [];
                    fondos.forEach(function (fondo, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            cuenta__cuenta: fondo.cuentaFondo.cuenta,
                            id_fondo: fondo.descripcion,
                            saldo:  '$'+parseFloat(fondo.saldo).formatMoney(2, '.', ','),
                            buttons: $.extend({}, {
                                edit: self.$root.can('editar_cuenta_fondo') ? true : undefined,
                                id: fondo.cuenta__id
                            })
                        })
                    })
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
        },
    }
</script>