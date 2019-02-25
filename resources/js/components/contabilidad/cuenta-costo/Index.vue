<template>
    <div class="row">
        <div class="col-12">
            <cuenta-costo-create @created="paginate()"></cuenta-costo-create>
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
                <div class="card-body">
                    <div class="table-responsive">
                        <datatable v-bind="$data" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import CuentaCostoCreate from "./Create";
    export default {
        name: "cuenta-costo-index",
        components: {CuentaCostoCreate},
        data() {
            return {
                timer: null,
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Costo', field: 'descripcion', sortable: true },
                    { title: 'Cuenta Contable', field: 'cuenta', sortable: true },
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons')},
                ],
                data: [],
                total: 0,
                query: {
                    include: 'cuenta',
                    scope: 'conCuenta'
                },
                search: ''
            }
        },

        computed: {
            meta() {
                return this.$store.getters['cadeco/costo/meta']
            },

            costos() {
                return this.$store.getters['cadeco/costo/costos']
            }
        },

        methods: {
            paginate() {
                let self = this
                self.$store.commit('cadeco/costo/SET_COSTOS', []);

                return self.$store.dispatch('cadeco/costo/paginate', self.query)
                    .then(data => {
                        self.$store.commit('cadeco/costo/SET_COSTOS', data.data);
                        self.$store.commit('cadeco/costo/SET_META', data.meta);
                    })
            }
        },

        mounted() {
            this.paginate()
        },

        watch: {
            meta: {
                handler (meta) {
                    this.total = meta.pagination.total
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
                    this.paginate();
                }, 500);
            },

            costos: {
                handler(costos) {
                    let self = this
                    self.$data.data = []
                    costos.forEach(function (costo, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            descripcion: costo.descripcion,
                            cuenta: costo.cuenta.cuenta,
                            buttons: $.extend({}, {
                                edit: self.$root.can('editar_cuenta_costo') ? true : undefined,
                                id: costo.cuenta.id
                            })
                        })
                    });
                },
                deep: true
            }
        }
    }
</script>
