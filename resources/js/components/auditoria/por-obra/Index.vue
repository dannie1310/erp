<template>
    <span>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="configuracion_obra_id">Seleccionar Obra</label>
                            <select
                                    @change="paginate()"
                                    :disabled="configuracionesObra.length == 0"
                                    name="configuracion_obra_id"
                                    class="form-control"
                                    id="configuracion_obra_id"
                                    v-model="id_configuracion_obra"
                            >
                                <option value>--- Seleccionar Obra ---</option>
                                <option v-for="obra in configuracionesObra" :value="obra.id">{{ `${obra.nombre} ` }} ({{ `${obra.base_datos} ` }})</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="card" id="Auditoria">
            <div class="row">

                    <div class="col-12">
                        <div class="card">
                            <!-- /.card-header -->
                            <div class="card-body">
                                 <div class="form-group error-content" style=" width: 100%;">
                                     <form role="form">
                                        <div class="modal-body">
                                           <div class="col-md-6">

                                           </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="row">
                                    <div class="table-responsive">
                                        <datatable v-bind="$data" />
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                <!-- /.col -->
            </div>
        </div>
    </span>

</template>

<script>
    export default {
        name: "por-obra-index",
        data() {
            return {
                id_configuracion_obra: '',
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Permiso', field: 'permiso', sortable: false, thComp: require('../../globals/th-Filter')},
                    { title: 'Rol', field: 'rol', sortable: false, thComp: require('../../globals/th-Filter') },
                    { title: 'Sistema', field: 'sistema', sortable: false , thComp: require('../../globals/th-Filter')},
                    { title: 'Usuario', field: 'usuario', sortable: false, thComp: require('../../globals/th-Filter') },
                    { title: 'Nombre de Usuario', field: 'nombre', sortable: false , thComp: require('../../globals/th-Filter') },
                    { title: 'Usuario que Asignó', field: 'asigno', sortable: false},
                    { title: 'Fecha de Asignación', field: 'fecha_asigno', sortable: false }
                ],
                data: [],
                total: 0,
                query: {},
                cargando: false
            }
        },

        mounted() {
            this.$Progress.start();
            this.query.sort = 'permiso';
            this.query.order = 'desc';
            this.getConfiguraciones()
                .finally(() => {
                    this.$Progress.finish();
                })
        },

        methods: {
            getConfiguraciones() {
                this.cargando = true;
                return this.$store.dispatch('seguridad/configuracion-obra/index', )
                    .then(data => {
                        this.$store.commit('seguridad/configuracion-obra/SET_CONFIGURACIONES', data.data);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },

            paginate() {
                if (this.id_configuracion_obra) {
                    this.cargando = true;

                    return this.$store.dispatch('seguridad/permiso/porObra', {
                        params: this.query,
                        id: this.id_configuracion_obra
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
                }
            }
        },

        computed: {
            configuracionesObra() {
                return  _.sortBy(this.$store.getters['seguridad/configuracion-obra/configuracionesObra'], ['base_datos', 'nombre']);
            },

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
                        permiso: permiso.permiso,
                        rol: permiso.rol,
                        sistema: permiso.sistema,
                        usuario: permiso.usuario,
                        nombre: permiso.nombre_completo_usuario,
                        asigno: permiso.usuario_asigno,
                        fecha_asigno: permiso.fecha_hora_asignacion

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