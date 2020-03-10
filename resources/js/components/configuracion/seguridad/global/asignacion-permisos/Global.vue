<template>
    <span>
        <div class="card" id="asignar-permisos">
            <div class="card-header">
                <h6 class="card-title">Asignación de Permisos</h6>
            </div>

            <div class="card-body">
                <div class="form-group row">
                    <label for="role_id" class="col-lg-2 col-form-label">Buscar Rol</label>
                    <div class="col-lg-10">
                        <rol-select
                                name="role_id"
                                id="role_id"
                                data-vv-as="Rol"
                                v-validate="{required: true, integer: true}"
                                v-model="form.role_id"
                                :error="errors.has('role_id')"
                        >
                        </rol-select>
                        <div class="error-label" v-show="errors.has('role_id')">{{ errors.first('role_id') }}</div>
                    </div>
                </div>

                <div class="row" v-if="form.role_id">
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="form-group">
                                    <label for="from">PERMISOS ASIGNADOS</label>
                                    <select multiple id="from" size="10" class="form-control" v-model="form.permission_id" :disabled="cargando">
                                        <option v-for="permiso in permisos_asignados_ordered" :value="permiso.id">{{ permiso.display_name }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="container col-sm-2">
                                <div class="vertical-center align-content-center">
                                    <button class="btn col-xs-12 btn-default" title="Agregar" @click="agregar"><i class="fa fa-long-arrow-left"></i></button>
                                    <button class="btn col-xs-12 btn-default" title="Quitar" @click="quitar"><i class="fa fa-long-arrow-right"></i></button>
                                </div>
                            </div>

                            <div class="col-sm-5">
                                <div class="form-group">
                                    <label for="to">PERMISOS DISPONIBLES </label>
                                    <select multiple id="to" size="10" class="form-control" v-model="selected" :disabled="cargando">
                                        <option v-for="permiso in permisos_disponibles_ordered" :value="permiso.id">{{ permiso.display_name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                 <div>
                     <button class="btn btn-outline-success float-right" :disabled="!permisos_desasignados.length && !permisos_nuevos_asignados.length" @click="validate"><i class="fa fa-save"></i></button>
                 </div>
            </div>
        </div>

        <div class="modal" ref="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detalle de Asignación</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table">
                                <tr>
                                    <th>Rol:</th>
                                    <td>{{ rol_seleccionado }}</td>
                                </tr>
                                <tr v-if="permisos_nuevos_asignados.length">
                                    <th>Permisos a Asignar:</th>
                                    <td>
                                        <ul>
                                            <li v-for="permiso in permisos_nuevos_asignados">{{ permiso.display_name }}</li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr v-if="permisos_desasignados.length">
                                    <th>Permisos a Desasignar:</th>
                                    <td>
                                        <ul>
                                            <li v-for="permiso in permisos_desasignados">{{ permiso.display_name }}</li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" @click="guardar" :disabled="guardando">
                            <span v-if="guardando">
                                <i class="fa fa-spin fa-spinner"></i>
                            </span>
                            <span v-else>
                                <i class="fa fa-save"></i> Guardar
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import RolSelect from "../../../../seguridad/global/rol/Select";
    export default {
        name: "asignacion-permisos-global",
        components: {RolSelect},
        data() {
            return {
                form: {
                    role_id: '',
                    permission_id: []
                },
                permisos_disponibles: [],
                permisos_asignados: [],
                permisos_originales: [],
                rol_seleccionado: '',
                cargando: false,
                guardando: false,
                selected: []
            }
        },

        mounted() {
            this.getPermisos();
        },

        methods: {
            getPermisos() {
                return this.$store.dispatch('seguridad/permiso/index')
                    .then(data => {
                        this.permisos_disponibles = data.data.sort((a, b) => (a.display_name > b.display_name) ? 1 : -1);
                    })
            },

            getPermisosPorRol(role_id) {
                this.permisos_originales = [];
                this.permisos_disponibles = this.permisos_disponibles.concat(this.permisos_asignados);
                return this.$store.dispatch('seguridad/permiso/index', {
                    config: { params: { scope: 'porRol:' + role_id } }
                })
                    .then(data => {
                        data.data.forEach(perm=> {
                            this.permisos_originales.push(perm.id);
                        })

                        this.permisos_asignados = data.data.sort((a, b) => (a.display_name > b.display_name) ? 1 : -1);
                        this.permisos_disponibles = this.permisos_disponibles.diff(this.permisos_asignados);
                    });
            },

            agregar() {
                this.selected.forEach(permiso => {
                    this.permisos_disponibles.forEach(r => {
                        if(r.id == permiso) {
                            this.permisos_asignados.push(r)
                            this.permisos_disponibles = this.permisos_disponibles.filter(perm => {
                                return perm.id != r.id;
                            });
                        }
                    })
                })
            },

            quitar() {
                this.form.permission_id.forEach(permiso => {
                    this.permisos_asignados.forEach(r => {
                        if(r.id == permiso) {
                            this.permisos_disponibles.push(r)
                            this.permisos_asignados = this.permisos_asignados.filter(perm => {
                                return perm.id != r.id;
                            });
                        }
                    })
                })
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.save();
                    }
                });
            },

            save() {
                this.$store.dispatch('seguridad/rol/find', {
                    id: this.form.role_id
                })
                    .then(data => {
                        this.rol_seleccionado = data.display_name;
                        $(this.$refs.modal).appendTo('body')
                        $(this.$refs.modal).modal('show');
                    })
            },

            guardar() {
                this.guardando = true;
                return this.$store.dispatch('seguridad/rol/asignarPermisos', {
                    role_id: this.form.role_id,
                    permission_id: this.permisos_asignados.map(permiso => (
                        permiso.id
                    ))
                })
                    .finally(() => {
                        $(this.$refs.modal).modal('hide');
                        this.$validator.reset()
                        this.guardando = false;
                        this.permisos_originales = this.permisos_asignados.map(perm => (
                            perm.id
                        ))
                    });
            }
        },

        watch: {
            'form.role_id'(id) {
                this.$validator.reset()
                if (id) {
                    this.cargando = true;
                    this.getPermisosPorRol(id)
                        .finally(() => {
                            this.cargando = false;
                        });
                }
            },
        },

        computed: {
            permisos_disponibles_ordered() {
                return this.permisos_disponibles.sort((a,b) => {
                    return (a.display_name<b.display_name?-1:(a.display_name>b.display_name?1:0));
                });
            },
            permisos_asignados_ordered() {
                return this.permisos_asignados.sort((a,b) => {
                    return (a.display_name<b.display_name?-1:(a.display_name>b.display_name?1:0));
                });
            },

            permisos_desasignados() {
                return this.permisos_disponibles.filter(permiso => {
                    return $.inArray(permiso.id, this.permisos_originales) > -1;
                })
            },

            permisos_nuevos_asignados() {
                return this.permisos_asignados.filter(permiso => {
                    return $.inArray(permiso.id, this.permisos_originales) == -1;
                })
            }
        }
    }
</script>

<style>
    .error-label {
        width: 100%;
        margin-top: 0.25rem;
        font-size: 80%;
        color: #dc3545;
    }
    .vue-treeselect__placeholder {
        color: #495057
    }

    .container {
        height: auto;
        position: relative;
        min-height: 50px;
    }

    .vertical-center {
        position: relative;
        top: 50%;
        -ms-transform: translateY(-50%);
        transform: translateY(-50%);
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
