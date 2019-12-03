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
        name: "cierre-periodo-index",
        components: {Create},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'id', sortable: false },
                    { title: 'Año', field: 'anio', sortable: true },
                    { title: 'Mes', field: 'mes', sortable: true },
                    { title: 'Persona que cerró', field: 'registro', sortable: false },
                    { title: 'Fecha de Cierre', field: 'fecha_cierre', sortable: true },
                    { title: 'Estatus', field: 'estatus', tdComp: require('./partials/CierreEstatus')},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {},
                estado: "",
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
            paginate(payload = {}) {
                this.cargando = true;
                return this.$store.dispatch('contabilidad/cierre-periodo/paginate', {
                    params: {
                        ...payload,
                        sort: 'id',
                        order: 'desc'
                    }
                })
                    .then(data => {
                        this.$store.commit('contabilidad/cierre-periodo/SET_CIERRES', data.data);
                        this.$store.commit('contabilidad/cierre-periodo/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },

            getMes(mes) {
                switch (mes) {
                    case 1 :
                        return "ENERO";
                    case 2 :
                        return "FEBRERO";
                    case 3 :
                        return "MARZO";
                    case 4 :
                        return "ABRIL";
                    case 5 :
                        return "MAYO";
                    case 6 :
                        return "JUNIO";
                    case 7 :
                        return "JULIO";
                    case 8 :
                        return "AGOSTO";
                    case 9 :
                        return "SEPTIEMBRE";
                    case 10 :
                        return "OCTUBRE";
                    case 11 :
                        return "NOVIEMBRE";
                    case 12 :
                        return "DICIEMBRE";
                }
            }
        },
        computed: {
            cierres(){
                return this.$store.getters['contabilidad/cierre-periodo/cierres'];
            },
            meta(){
                return this.$store.getters['contabilidad/cierre-periodo/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            cierres: {
                handler(cierres) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = cierres.map((cierre, i) => ({
                        id: (i + 1) + self.query.offset,
                        anio: cierre.anio,
                        mes: self.getMes(cierre.mes),
                        registro: cierre.usuario.nombre,
                        fecha_cierre: cierre.fecha,
                        estatus: cierre.abierto,
                        buttons: $.extend({}, {
                            cerrar: self.$root.can('editar_cierre_periodo') && cierre.abierto == 1 ? true : undefined,
                            abrir: self.$root.can('editar_cierre_periodo') && !cierre.abierto == 1 ? true : undefined,
                            id: cierre.id
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
                handler (query) {
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
        },
    }
</script>