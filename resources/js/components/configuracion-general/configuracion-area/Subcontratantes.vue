<template>
    <span>
        <div class="card" id="subcontratantes">
            <div class="card-header">
                <h6 class="card-title">Asignación de Áreas Subcontratantes</h6>
            </div>

            <div class="card-body">
                <div class="form-group row">
                    <label for="user_id" class="col-lg-2 col-form-label">Buscar Usuario</label>
                    <div class="col-lg-8">
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
                    <div class="col-sm-10">
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="form-group">
                                    <label for="from">AREAS SUBCONTRATANTES A ASIGNAR</label>
                                    <select multiple id="from" size="10" class="form-control" v-model="form.area_id" :disabled="cargando">
                                        <option v-for="area in areas_asignados_ordered" :value="area.id">{{ area.descripcion }}</option>
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
                                    <label for="to">AREAS SUBCONTRATANTES DISPONIBLES</label>
                                    <select multiple id="to" size="10" class="form-control" v-model="selected">
                                        <option v-for="area in areas_disponibles_ordered" :value="area.id">{{ area.descripcion }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                <button class="btn btn-outline-success float-right" :disabled="!areas_desasignados.length && !areas_nuevos_asignados.length" @click="validate"><i class="fa fa-save"></i></button>
                </div>
            </div>

        </div>
        <div class="modal" ref="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detalle de Asignación/Desasignación</h5>
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
                                 <tr  v-if="areas_nuevos_asignados.length">
                                    <th>Areas Subcontratantes a Asignar:</th>
                                    <td>
                                        <ul>
                                            <li v-for="area in areas_nuevos_asignados">{{ area.descripcion }}</li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr v-if="areas_desasignados.length">
                                    <th>Areas Subcontratantes a Desasignar:</th>
                                    <td>
                                        <ul>
                                            <li v-for="area in areas_desasignados">{{ area.descripcion }}</li>
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
                                <i class="fa fa-save"></i> Aplicar
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </span>

</template>

<script>
    import UsuarioSelect from "../../igh/usuario/Select";
    export default {
        name: "subcontratantes",
        components: {UsuarioSelect},
        data() {
            return {
                form: {
                    user_id: '',
                    area_id: []
                },
                cargando: false,
                guardando: false,
                selected: [],
                areas_disponibles: [],
                areas_asignados: [],
                areas_originales:[],
                usuario_seleccionado: ''
            }
        },
        mounted() {
            this.getAreaSub();
            $(this.$refs.modal).on('hidden.bs.modal', () => {
                this.usuario_seleccionado = '';
            })
        },
        methods: {
            addArea(data){
                this.areas_disponibles.push(data);
                this.areas_disponibles = this.areas_disponibles.sort((a, b) => (a.descripcion > b.descripcion) ? 1 : -1);
            },

            getAreaSub() {
                this.areas_disponibles = [];
                return this.$store.dispatch('configuracion/area-subcontratante/index')
                    .then(data => {
                        this.areas_disponibles = data.sort((a, b) => (a.descripcion > b.descripcion) ? 1 : -1);
                    });
            },

            getAreasUsuario(user_id) {
                this.areas_originales = []
                this.areas_disponibles = this.areas_disponibles.concat(this.areas_asignados);
                return this.$store.dispatch('configuracion/area-subcontratante/getAreasUsuario', user_id)
                    .then(data => {
                        data.data.forEach(perm=> {
                            this.areas_originales.push(perm.id);
                        })

                        this.areas_asignados = data.data.sort((a, b) => (a.descripcion > b.descripcion) ? 1 : -1);
                        this.areas_disponibles = this.areas_disponibles.diff(this.areas_asignados);
                    });
            },

            agregar() {
                this.selected.forEach(area => {
                    this.areas_disponibles.forEach(a => {
                        if(a.id == area) {
                            this.areas_asignados.push(a)
                            this.areas_disponibles = this.areas_disponibles.filter(areas => {
                                return areas.id != a.id;
                            });
                        }
                    })
                })
            },

            quitar() {
                this.form.area_id.forEach(area => {
                    this.areas_asignados.forEach(a => {
                        if(a.id == area) {
                            this.areas_disponibles.push(a)
                            this.areas_asignados = this.areas_asignados.filter(areas => {
                                return areas.id != a.id;
                            });
                        }
                    })
                })
            },

            asignar() {
                this.guardando = true;
                return this.$store.dispatch('configuracion/area-subcontratante/asignacionAreasSubcontratantes', {
                    user_id: this.form.user_id,
                    area_id: this.areas_asignados.map(area => (
                        area.id
                    ))
                })
                    .then(data => {
                        if (this.currentUser.idusuario == this.form.user_id) {
                            this.$session.set('permisos', data)
                        }
                        this.areas_originales = this.areas_asignados.map(area => (
                            area.id
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
                        $(this.$refs.modal).appendTo('body')
                        $(this.$refs.modal).modal('show');
                    })
            }
        },

        computed:{
            currentUser(){
                return this.$store.getters['auth/currentUser']
            },
            areas_asignados_ordered() {
                return this.areas_asignados.sort((a,b) => {
                    return (a.descripcion<b.descripcion?-1:(a.descripcion>b.descripcion?1:0));
                });
            },

            areas_disponibles_ordered() {
                return this.areas_disponibles.sort((a,b) => {
                    return (a.descripcion<b.descripcion?-1:(a.descripcion>b.descripcion?1:0));
                });
            },
            areas_desasignados() {
                return this.areas_disponibles.filter(areas => {
                    return $.inArray(areas.id, this.areas_originales) > -1;
                })
            },

            areas_nuevos_asignados() {
                return this.areas_asignados.filter(areas => {
                    return $.inArray(areas.id, this.areas_originales) == -1;
                })
            }
        },watch: {
            'form.user_id'(id) {
                this.$validator.reset()
                if (id) {
                    this.cargando = true;
                    this.getAreasUsuario(id)
                        .finally(() => {
                            this.cargando = false;
                        });
                }
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
