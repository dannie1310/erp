<template>
    <span>
        <div class="card" id="asignacion">
            <div class="card-header">
                <h3 class="card-title">Asignación de Roles</h3>
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
                                    <label for="from">ROLES A ASIGNAR</label>
                                    <select multiple id="from" size="10" class="form-control" v-model="form.role_id">
                                        <option v-for="rol in roles_asignados" :value="rol.id">{{ rol.display_name }}</option>
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
                                    <label for="to">ROLES DISPONIBLES</label>
                                    <select multiple id="to" size="10" class="form-control" v-model="selected">
                                        <option v-for="rol in roles_disponibles" :value="rol.id">{{ rol.display_name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                <button class="btn btn-outline-success pull-right" :disabled="!roles_asignados.length" @click="validate"><i class="fa fa-save"></i></button>
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
                                    <th>Usuario:</th>
                                    <td>{{ usuario_seleccionado }}</td>
                                </tr>
                                 <tr>
                                    <th>Roles a Asignar:</th>
                                    <td>
                                        <ul>
                                            <li v-for="rol in roles_asignados">{{ rol.display_name }}</li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" @click="asignar" :disabled="guardando">
                            <span v-if="guardando">
                                <i class="fa fa-spin fa-spinner"></i>
                            </span>
                            <span v-else>
                                <i class="fa fa-save"></i> Asignar
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
        name: "asignacion-roles-personalizado",
        components:{UsuarioSelect},
        data() {
            return {
                form: {
                    id_proyecto: '',
                    tipo_asignacion: 2,
                    user_id: '',
                    role_id: []
                },
                obras: null,
                cargando: false,
                guardando: false,
                selected: [],
                roles_disponibles: [],
                roles_asignados: [],
                usuario_seleccionado: ''
            }
        },
        mounted() {
            this.getRoles();
            $(this.$refs.modal).on('hidden.bs.modal', () => {
                this.usuario_seleccionado = '';
            })
        },
        methods: {
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

            getRolesUsuario(user_id) {
                this.roles_disponibles = this.roles_disponibles.concat(this.roles_asignados);
                return this.$store.dispatch('seguridad/rol-personalizado/getRolesUsuario', user_id)
                    .then(data => {
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

            asignar() {
                this.guardando = true;
                return this.$store.dispatch('seguridad/rol-personalizado/asignacionMasiva', {
                    //id_proyecto: Array.isArray(this.form.id_proyecto) ? this.form.id_proyecto : [this.form.id_proyecto],
                    user_id: this.form.user_id,
                    role_id: this.roles_asignados.map(rol => (
                        rol.id
                    ))
                })
                    .finally(() => {
                        this.guardando = false;
                        this.roles_disponibles = this.roles_disponibles.concat(this.roles_asignados)
                        this.roles_asignados = [];
                        this.form.id_proyecto = this.form.tipo_asignacion == 2 ? [] : '';
                        this.form.role_id = [];
                        $(this.$refs.modal).modal('hide');
                        this.$validator.reset()
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
                if(id) {
                    this.getRolesUsuario(id);
                }
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