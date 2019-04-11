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

            <fieldset class="form-group">
                <div class="row">
                    <legend class="col-form-label col-lg-2 pt-0"><b>Tipo de Asignación</b></legend>
                    <div class="col-lg-10">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" value="1" v-model="form.tipo_asignacion">
                            <label class="form-check-label"> Masiva</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" value="2" v-model="form.tipo_asignacion">
                            <label class="form-check-label"> Por Proyecto</label>
                        </div>
                    </div>
                </div>
            </fieldset>

            <div class="row" v-if="form.user_id">
                <div class="col-sm-4">
                    <div class="form form-group">
                        <label for="id_proyecto">{{ form.tipo_asignacion == 1 ? 'Proyectos' : 'Proyecto' }}</label>
                        <select id="id_proyecto" size="10" class="form-control" :multiple="form.tipo_asignacion == 1" :disabled="!obras" v-model="form.id_proyecto"
                                v-validate="{required: true}"
                                name="id_proyecto"
                                :data-vv-as="form.tipo_asignacion == 1 ? 'Proyectos' : 'Proyecto'"
                                :class="{'is-invalid': errors.has('id_proyecto')}"
                        >
                            <optgroup :label="i" v-for="(grupo, i) in obras_agrupadas">
                                <option v-for="obra in grupo" :value="`${i}-${obra.id_obra}`">{{ obra.nombre }}</option>
                            </optgroup>
                        </select>
                        <div class="invalid-feedback" v-show="errors.has('id_proyecto')">{{ errors.first('id_proyecto') }}</div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="row">
                        <div class="col-sm-5">
                            <div class="form-group">
                                <label for="from">{{ form.tipo_asignacion == 1 ? 'ROLES A DESASIGNAR' : 'ROLES ASIGNADOS' }}</label>
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
                                <label for="to">{{ form.tipo_asignacion == 1 ? 'ROLES DISPONIBLES' : 'ROLES NO ASIGNADOS' }} </label>
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
                                </tr>
                                 <tr>
                                    <th>Roles a Desasignar:</th>
                                    <td>
                                        <ul>
                                            <li v-for="rol in roles_desasignados">{{ rol.display_name }}</li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Proyectos:</th>
                                    <td>
                                        <ul>
                                            <li v-for="obra in obras_seleccionadas">{{ `[${obra.base_datos}] ${obra.nombre}` }}</li>
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
        name: "desasignacion-roles-masiva",
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
            getObrasPorUsuario(id) {
                return this.$store.dispatch('cadeco/obras/getObrasPorUsuario', {
                    user_id: id
                });
            },

            getRoles() {
                return this.$store.dispatch('seguridad/rol/index')
                    .then(data => {
                        this.roles_disponibles = data.sort((a, b) => (a.display_name > b.display_name) ? 1 : -1);
                    });
            },

            getRolesUsuario(data) {
                this.roles_originales = [];
                this.roles_disponibles = this.roles_disponibles.concat(this.roles_asignados);
                return this.$store.dispatch('seguridad/rol/getRolesUsuario', data)
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
                return this.$store.dispatch('seguridad/rol/desasignacionMasiva', {
                    id_proyecto: Array.isArray(this.form.id_proyecto) ? this.form.id_proyecto : [this.form.id_proyecto],
                    user_id: this.form.user_id,
                    role_id: this.roles_desasignados.map(rol => (
                        rol.id
                    ))
                })
                    .finally(() => {
                        this.guardando = false;
                        this.roles_disponibles = this.roles_disponibles.concat(this.roles_asignados)
                        this.roles_asignados = [];
                        this.form.id_proyecto = this.form.tipo_asignacion == 1 ? [] : '';
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
                this.form.id_proyecto = [];
                this.obras = null;
                this.$validator.reset()

                if (id) {
                    this.cargando = true;
                    this.getObrasPorUsuario(id)
                        .then(data => {
                            this.obras = data.data;
                        })
                        .finally(() => {
                            this.cargando = false;
                        })
                }
            },

            'form.tipo_asignacion'(tipo) {
                this.roles_disponibles = this.roles_disponibles.concat(this.roles_asignados);
                this.roles_asignados = [];

                if (tipo == 1) {
                    this.form.id_proyecto = [];
                } else if (tipo == 2) {
                    this.form.id_proyecto = '';
                }
                this.$validator.reset();
            },

            'form.id_proyecto'(id) {
                if (id) {
                    if (this.form.tipo_asignacion == 2) {
                        this.getRolesUsuario({
                            user_id: this.form.user_id,
                            params: {
                                id_obra: id.split('-')[1],
                                base_datos: id.split('-')[0],
                            }
                        })
                    }
                }
            }
        },

        computed: {
            obras_agrupadas() {
                if (this.obras)
                    return _.groupBy(this.obras, 'base_datos');
                else
                    return [];
            },

            roles_desasignados() {
                if (this.form.tipo_asignacion == 1) {
                    return this.roles_asignados;
                } else {
                    return this.roles_disponibles.filter(rol => {
                        return $.inArray(rol.id, this.roles_originales) > -1;
                    })
                }
            },

            obras_seleccionadas() {
                if (this.form.id_proyecto && this.obras) {
                    if (Array.isArray(this.form.id_proyecto)) {
                        return this.obras.filter(obra => {
                            return  $.inArray(`${obra.base_datos}-${obra.id_obra}`, this.form.id_proyecto) > -1;
                        })
                    } else {
                        return this.obras.filter(obra => {
                            return  `${obra.base_datos}-${obra.id_obra}` === this.form.id_proyecto;
                        })
                    }
                }  else {
                    return [];
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