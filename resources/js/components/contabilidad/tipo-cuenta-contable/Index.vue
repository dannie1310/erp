<template>
    <div class="row">
        <div class="col-12">
            <tipo-cuenta-contable-create></tipo-cuenta-contable-create>
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
    import TipoCuentaContableCreate from "./Create";
    export default {
        name: "tipo-cuenta-contable-index",
        components: {TipoCuentaContableCreate},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Descripcion', field: 'descripcion', sortable: true },
                    { title: 'Registró', field: 'registro', sortable: false },
                    { title: 'Naturaleza de la Cuenta', field: 'naturaleza', sortable: true },
                ],
                data: [],
                total: 0,
                query: {
                },
                naturaleza: ''
            }
        },

        mounted() {
            this.paginate(),
            this.$store.dispatch('contabilidad/naturaleza-poliza/index')
        },

        methods: {
            paginate(payload = {}) {
                return this.$store.dispatch('contabilidad/tipo-cuenta-contable/paginate', {
                    ...payload,
                    include: ['naturaleza', 'usuario']
                })
            }
        },
        computed: {
            tipos(){
                return this.$store.getters['contabilidad/tipo-cuenta-contable/tipos'];
            },
            meta(){
                return this.$store.getters['contabilidad/tipo-cuenta-contable/meta'];
            },
        },
        watch: {
            tipos: {
                handler(tipos) {
                    let self = this
                    self.$data.data = []
                    tipos.forEach(function (tipo, i) {
                        if(typeof tipo.naturaleza  !== 'undefined') {
                            self.$data.naturaleza = tipo.naturaleza.descripcion;
                        }else{
                            self.$data.naturaleza = '';
                        }
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            descripcion: tipo.descripcion,
                            registro: tipo.usuario.nombre,
                            naturaleza: self.$data.naturaleza
                        })
                    });
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
                handler (query) {
                    this.paginate(query)
                },
                deep: true
            }
        },
    }
</script>