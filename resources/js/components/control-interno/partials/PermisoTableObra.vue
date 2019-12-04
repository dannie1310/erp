<template>
    <div class="card">
        <div class="card-header border-transparent">
            <h6 class="card-title">Informe de permisos</h6>

            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <datatable v-bind="$data" />
            </div>
            <!-- /.table-responsive -->
        </div>
        <!-- /.card-body -->
    </div>
</template>

<script>
    export default {
        name: "permiso-table-obra",
        data() {
            return {
                id_configuracion_obra: '',
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Usuario', field: 'usuario', sortable: true, thComp: require('../../globals/th-Filter').default},
                    { title: 'Nombre de Usuario', field: 'nombre_completo', sortable: true, thComp: require('../../globals/th-Filter').default },
                    { title: 'Ubicación', field: 'ubicacion', sortable: true , thComp: require('../../globals/th-Filter').default},
                    { title: 'Departamento', field: 'departamento', sortable: true, thComp: require('../../globals/th-Filter').default },
                    { title: 'Usuario Corporativo', field: 'factor_es_corporativo', sortable: true, tdComp: require('./CorporativoEstatus') },
                    { title: 'Cantidad de Permisos', field: 'cantidad_permisos', sortable: true },
                    { title: 'Cantidad de Proyectos', field: 'cantidad_obras', sortable: true },
                    { title: 'Estatus', field: 'factor_orden', sortable: true, tdComp: require('./SemaforoEstatus') }
                ],
                data: [],
                total: 0,
                query: {},
                cargando: false
            }
        },
        mounted() {
            this.paginate();
        },
        methods: {
            paginate() {
                this.cargando = true;

                return this.$store.dispatch('seguridad/permiso/porCantidad', {
                    params: this.query
                })
                    .then(data => {
                        this.$store.commit('seguridad/permiso/SET_PERMISOS',data.data);
                        this.$store.commit('seguridad/permiso/SET_META', {
                            "pagination": {
                                "total": data.total,
                                "count": data.to - data.from + 1,
                                "per_page": data.per_page,
                                "current_page": data.current_page,
                                "total_pages": data.last_page,
                            }
                        });
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
        },
        computed: {
            permisos() {
                return this.$store.getters['seguridad/permiso/permisos'];
            },

            meta(){
                return this.$store.getters['seguridad/permiso/meta'];
            },

            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            },

        },

        watch: {
            permisos: {
                handler(perms) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = perms.map((permiso, i) => ({
                        index: (i + 1) + self.query.offset,
                        usuario: permiso.usuario,
                        nombre_completo: permiso.nombre_completo,
                        ubicacion: permiso.ubicacion,
                        departamento: permiso.departamento,
                        factor_es_corporativo: permiso.factor_es_corporativo,
                        cantidad_permisos: permiso.cantidad_permisos,
                        cantidad_obras: permiso.cantidad_obras,
                        factor_orden: permiso.factor_orden

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
                    this.paginate()
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

<style scoped>

</style>