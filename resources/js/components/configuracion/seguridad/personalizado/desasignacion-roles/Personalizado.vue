<template>
    <span>
       <div class="card" id="desasignacion">
        <div class="card-header">
            <h3 class="card-title">Desasignación de Roles</h3>
        </div>

        <div class="card-body">
            <div class="form-group row">
                <label for="user_id" class="col-lg-2 col-form-label">Buscar Usuario</label>
                <div class="col-lg-6">
                    <usuario-select
                            name="user_id"
                            id="user_id"
                            data-vv-as="Usuario"
                            v-validate="{required: true, integer: true}"
                            v-model="form.user_id"
                            :error="errors.has('user_id')"
                    >
                    </usuario-select>
                    <div class="error-label" v-show="errors.has('user_id')">{{ errors.first('user_id') }}</div>
                </div>
            </div>

            <div class="row" v-if="form.user_id">

                <div class="col-sm-8">
                    <div class="row">
                        <div class="col-sm-5">
                            <div class="form-group">
                                <label for="from">ROLES A DESASIGNAR</label>
                                <select multiple id="from" size="10" class="form-control" v-model="form.role_id" :disabled="cargando">
                                    <option v-for="rol in roles_asignados_ordered" :value="rol.id">{{ rol.display_name }}</option>

                                </select>
                            </div>
                        </div>

                        <div class="container col-sm-2">
                            <div class="vertical-center align-content-center">
                                <button class="btn col-xs-12 btn-default" @click="agregar" title="Agregar"><i class="fa fa-long-arrow-left"></i></button>
                                <button class="btn col-xs-12 btn-default" @click="quitar" title="Quitar"><i class="fa fa-long-arrow-right"></i></button>
                            </div>
                        </div>

                        <div class="col-sm-5">
                            <div class="form-group">
                                <label for="to">ROLES NO ASIGNADOS</label>
                                <select multiple id="to" size="10" class="form-control" v-model="selected">
                                    <option v-for="rol in roles_disponibles_ordered" :value="rol.id">{{ rol.display_name }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <button class="btn btn-outline-success pull-right"  :disabled="!roles_desasignados.length && !roles_nuevos_asignados.length" @click="validate"><i class="fa fa-save"></i></button>
            </div>
        </div>
    </div>

        <div class="modal" ref="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detalle de Desasignación</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table">
                                <tr>
                                    <th>Usuario:</th>
                                    <td>{{ usuario_seleccionado }}</td>
                                </tr v-if="roles_desasignados.length">
                                 <tr>
                                    <th>Roles a Desasignar:</th>
                                    <td>
                                        <ul>
                                            <li v-for="rol in roles_desasignados">{{ rol.display_name }}</li>
                                        </ul>
                                    </td>
                                </tr>

                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" @click="desasignar" :disabled="guardando">
                            <span v-if="guardando">
                                <i class="fa fa-spin fa-spinner"></i>
                            </span>
                            <span v-else>
                                <i class="fa fa-save"></i> Desasignar
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import UsuarioSelect from "../../../../igh/usuario/Select";
    export default {
        name: "desasignacion-roles-personalizado",
        components: {UsuarioSelect},
        data() {
            return {
                form: {
                    id_proyecto: '',
                    tipo_asignacion: 1,
                    user_id: '',
                    role_id: []
                },
                obras: null,
                cargando: false,
                guardando: false,
                selected: [],
                roles_disponibles: [],
                roles_originales: [],
                roles_asignados: [],
                usuario_seleccionado: '',
                roles_originales: []
            }
        },

        mounted() {
            this.getRoles();
            $(this.$refs.modal).on('hidden.bs.modal', () => {
                this.usuario_seleccionado = '';
            })
        },

        methods: {
            addRol(data){
                this.roles_disponibles.push(data);
                this.roles_disponibles = this.roles_disponibles.sort((a, b) => (a.display_name > b.display_name) ? 1 : -1);
            },
            getObrasPorUsuario(id) {
                return this.$store.dispatch('cadeco/obras/getObrasPorUsuario', {
                    user_id: id
                });
            },

            getRoles() {
                this.roles_disponibles = [];
                return this.$store.dispatch('seguridad/rol-personalizado/index')
                    .then(data => {
                        this.roles_disponibles = data.sort((a, b) => (a.display_name > b.display_name) ? 1 : -1);
                    });
            },

            getRolesUsuario(data) {
                this.roles_originales = [];
                this.roles_disponibles = this.roles_disponibles.concat(this.roles_asignados);
                return this.$store.dispatch('seguridad/rol-personalizado/getRolesUsuario', data)
                    .then(data => {
                        data.data.forEach(rol => {
                            this.roles_originales.push(rol.id);
                        });

                        this.roles_asignados = data.data.sort((a, b) => (a.display_name > b.display_name) ? 1 : -1);
                        this.roles_disponibles = this.roles_disponibles.diff(this.roles_asignados);
                    });
            },

            agregar() {
                this.selected.forEach(rol => {
                    this.roles_disponibles.forEach(r => {
                        if(r.id == rol) {
                            this.roles_asignados.push(r)
                            this.roles_disponibles = this.roles_disponibles.filter(role => {
                                return role.id != r.id;
                            });
                        }
                    })
                })
            },

            quitar() {
                this.form.role_id.forEach(rol => {
                    this.roles_asignados.forEach(r => {
                        if(r.id == rol) {
                            this.roles_disponibles.push(r)
                            this.roles_asignados = this.roles_asignados.filter(role => {
                                return role.id != r.id;
                            });
                        }
                    })
                })
            },

            desasignar() {
                this.guardando = true;
                return this.$store.dispatch('seguridad/rol-personalizado/desasignacionMasiva', {
                    //id_proyecto: Array.isArray(this.form.id_proyecto) ? this.form.id_proyecto : [this.form.id_proyecto],
                    user_id: this.form.user_id,
                    role_id: this.roles_asignados.map(rol => (
                        rol.id
                    ))
                })
                    .then(data => {
                        this.roles_originales = this.roles_asignados.map(rol => (
                            rol.id
                        ))
                    } )
                    .finally(() => {


                        $(this.$refs.modal).modal('hide');
                        this.guardando = false;
                    });
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.save()
                    }
                });
            },

            save() {
                this.$store.dispatch('igh/usuario/find', {
                    id: this.form.user_id
                })
                    .then(data => {
                        this.usuario_seleccionado = data.nombre;
                        $(this.$refs.modal).modal('show');
                    })
            }
        },

        watch: {
            'form.user_id'(id) {
                this.$validator.reset()
                if (id) {
                    this.cargando = true;
                    this.getRolesUsuario(id)
                        .finally(() => {
                            this.cargando = false;
                        });
                }
            }
        },

        computed:{
            roles_asignados_ordered() {
                return this.roles_asignados.sort((a,b) => {
                    return (a.display_name<b.display_name?-1:(a.display_name>b.display_name?1:0));
                });
            },
            roles_disponibles_ordered() {
                return this.roles_disponibles.sort((a,b) => {
                    return (a.display_name<b.display_name?-1:(a.display_name>b.display_name?1:0));
                });
            },
            roles_desasignados() {
                return this.roles_disponibles.filter(roles => {
                    return $.inArray(roles.id, this.roles_originales) > -1;
                })
            },

            roles_nuevos_asignados() {
                return this.roles_asignados.filter(roles => {
                    return $.inArray(roles.id, this.roles_originales) == -1;
                })
            }
        },

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