<template>
    <div class="card">
        <div class="card-header border-transparent">
            <h3 class="card-title">Información cantidad de permisos asignados a usuario por obras</h3>

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
                    { title: 'Usuario', field: 'usuario', sortable: false, thComp: require('../../globals/th-Filter')},
                    { title: 'Nombre de Usuario', field: 'nombre', sortable: false, thComp: require('../../globals/th-Filter') },
                    { title: 'Ubicación', field: 'ubicacion', sortable: false , thComp: require('../../globals/th-Filter')},
                    { title: 'Departamento', field: 'depto', sortable: false, thComp: require('../../globals/th-Filter') },
                    { title: 'Usuario Corporativo', field: 'corporativo', sortable: false, tdComp: require('./CorporativoEstatus') },
                    { title: 'Cantidad de Permisos', field: 'permisos', sortable: false },
                    { title: 'Cantidad de Proyectos', field: 'obras', sortable: false },
                    { title: 'Estatus', field: 'estatus', sortable: false, tdComp: require('./SemaforoEstatus') }
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
                        nombre: permiso.nombre_completo,
                        ubicacion: permiso.ubicacion,
                        depto: permiso.departamento,
                        corporativo: permiso.factor_es_corporativo,
                        permisos: permiso.cantidad_permisos,
                        obras: permiso.cantidad_obras,
                        estatus: permiso.factor_orden

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