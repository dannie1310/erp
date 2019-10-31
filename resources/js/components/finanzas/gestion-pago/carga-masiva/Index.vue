<template>
    <div class="row">
        <div class="col-12"  :disabled="cargando">
            <button  @click="create" title="Crear" class="btn btn-app btn-info pull-right" v-if="$root.can('registrar_carga_layout_pago')" >
                <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                <i class="fa fa-plus" v-else></i>
                Registrar Carga Masiva
            </button>
            <button  @click="descarga_layout" title="Crear" class="btn btn-app btn-info pull-right"  v-if="$root.can('descargar_layout_pagos')" >
                <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                <i class="fa fa-download" v-else></i>
                Descargar Layout
            </button>
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
    import Create from './Create';
    export default {
        name: "carga-masiva-index",
        components: {Create},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Folio', field: 'numero_folio', thComp: require('../../../globals/th-Filter'), sortable: true},
                    { title: 'Fecha', field: 'fecha', tdClass: 'fecha_hora', sortable: true},
                    { title: 'Monto', field: 'monto', tdClass: 'td_money', sortable: true},
                    { title: 'No. Doctos.', field: 'cantidad_documentos', tdClass: 'money', sortable: false},
                    { title: 'Usuario', field: 'usuario', sortable: true},
                    { title: 'Estado', field: 'estado', sortable: true},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons')},

                ],
                data: [],
                total: 0,
                query: {include: ['usuario','estado'], sort: 'id', order: 'desc'},
                estado: "",
                cargando: false,
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
            create() {
                this.$Progress.start();
                this.$router.push({name: 'carga-masiva-create'});
            },
            paginate() {
                this.cargando = true;
                return this.$store.dispatch('finanzas/carga-masiva-pago/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('finanzas/carga-masiva-pago/SET_LAYOUTS', data.data);
                        this.$store.commit('finanzas/carga-masiva-pago/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            descarga_layout(){
                return this.$store.dispatch('finanzas/carga-masiva-pago/descarga_layout', {})
                    .then(() => {
                        this.$emit('success')

                    })
            }
        },
        computed: {
            layouts(){
                return this.$store.getters['finanzas/carga-masiva-pago/layouts'];
            },
            meta(){
                return this.$store.getters['finanzas/carga-masiva-pago/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            layouts: {
                handler(layouts) {
                    let self = this
                    self.$data.data = []
                    layouts.forEach(function (layout, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            numero_folio:layout.id,
                            fecha: layout.fecha_registro,
                            monto: layout.monto_format,
                            cantidad_documentos: layout.cantidad_documentos,
                            usuario: layout.usuario.nombre,
                            estado:layout.estado.descripcion,
                            buttons: $.extend({}, {
                                id: layout.id,
                                autorizar: (layout.estado.estado == 0)?true:false,
                                show: true
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