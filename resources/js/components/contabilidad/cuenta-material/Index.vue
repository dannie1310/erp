<template>
    <div class="row">
        <div class="col-12">
            <cuenta-material-create @created="paginate()"></cuenta-material-create>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <select class="form-control" v-model="tipo_material">
                                    <option value>-- Tipo de Material --</option>
                                    <option v-for="tipo in tipos" :value="tipo.id">{{ tipo.descripcion }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
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
    import CuentaMaterialCreate from "./Create";
    export default {
        name: "cuenta-material-index",
        components: {CuentaMaterialCreate},
        data() {
            return {
                timer: null,
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Material', field: 'descripcion', sortable: true },
                    { title: 'Número de Parte', field: 'numero_parte', sortable: true },
                    { title: 'Tipo de Cuenta', field: 'tipo_cuenta', sortable: true },
                    { title: 'Cuenta Contable', field: 'cuenta', sortable: true },
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons')},
                ],
                data: [],
                total: 0,
                query: {
                    include: 'cuentaMaterial',
                    scope: 'conCuenta'
                },
                tipos: [
                    {id: 1, descripcion: 'Materiales'},
                    {id: 2, descripcion: 'Mano de Obra y Servicios'},
                    {id: 4, descripcion: 'Herramienta y Equipo'},
                    {id: 8, descripcion: 'Maquinaria'}
                ],
                tipo_material: '',
                search: ''
            }
        },

        computed: {
            meta() {
                return this.$store.getters['cadeco/material/meta']
            },

            materiales() {
                return this.$store.getters['cadeco/material/materiales']
            }
        },

        methods: {
            paginate() {
                let self = this
                self.$store.commit('cadeco/material/SET_MATERIALES', []);

                return self.$store.dispatch('cadeco/material/paginate', self.query)
                    .then(data => {
                        self.$store.commit('cadeco/material/SET_MATERIALES', data.data);
                        self.$store.commit('cadeco/material/SET_META', data.meta);
                    })
            }
        },

        mounted() {
            this.$store.dispatch('contabilidad/tipo-cuenta-material/index');
            this.paginate()
        },

        watch: {
            meta: {
                handler (meta) {
                    this.total = meta.pagination.total
                },
                deep: true
            },

            tipo_material(tipo) {
                this.query.scope = tipo ?  ['conCuenta', `tipo:${tipo}`] : 'conCuenta'
                this.paginate();
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

            materiales: {
                handler(materiales) {
                    let self = this
                    self.$data.data = []
                    materiales.forEach(function (material, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            descripcion: material.descripcion,
                            numero_parte: material.numero_parte,
                            cuenta: material.cuentaMaterial.cuenta,
                            tipo_cuenta: material.cuentaMaterial.tipo.descripcion,
                            buttons: $.extend({}, {
                                edit: self.$root.can('editar_cuenta_material') ? true : undefined,
                                id: material.cuentaMaterial.id
                            })
                        })
                    });
                },
                deep: true
            }
        }
    }
</script>
