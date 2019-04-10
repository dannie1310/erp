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
        name: "tipo-cuenta-contable-index",
        components: {Create},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Descripcion', field: 'descripcion', sortable: true },
                    { title: 'Registró', field: 'registro', sortable: false },
                    { title: 'Naturaleza de la Cuenta', field: 'id_naturaleza_poliza', sortable: true },
                    { title: 'Fecha y Hora de Registro', field: 'fecha_registro', sortable: false},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons')}
                ],
                data: [],
                total: 0,
                query: {include: ['naturaleza', 'usuario']},
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
            this.$store.dispatch('contabilidad/naturaleza-poliza/index')
        },

        methods: {
            paginate() {
                this.cargando = true;
                return this.$store.dispatch('contabilidad/tipo-cuenta-contable/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('contabilidad/tipo-cuenta-contable/SET_TIPOS', data.data);
                        this.$store.commit('contabilidad/tipo-cuenta-contable/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
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
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },

        watch: {
            tipos: {
                handler(tipos) {
                    let self = this
                    self.$data.data = []
                    tipos.forEach(function (tipo, i) {
                        if (typeof tipo.naturaleza !== 'undefined') {
                            self.$data.naturaleza = tipo.naturaleza.descripcion;
                        } else {
                            self.$data.naturaleza = '';
                        }
                        if(tipo.usuario){
                            self.$data.registro = tipo.usuario.nombre;
                        }else{
                            self.$data.registro = '';
                        }
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            descripcion: tipo.descripcion,
                            registro: self.$data.registro,
                            id_naturaleza_poliza: self.$data.naturaleza,
                            fecha_registro: tipo.fecha,
                            buttons: $.extend({}, {
                                show: true,
                                edit: self.$root.can('editar_tipo_cuenta_contable') ? true : undefined,
                                delete: self.$root.can('eliminar_tipo_cuenta_contable') ? true : undefined,
                                id: tipo.id
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