<template>
    <span>
        <div class="card" id="configuracion-sistema">
            <div class="card-header">
                <h3 class="card-title">Habilitación de Sistemas</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="form-group">
                                    <label for="from">SISTEMAS HABILITADOS</label>
                                   <select multiple id="from" size="10" class="form-control" v-model="form.sistemas_id" :disabled="cargando">
                                        <option v-for="sistema in sistemas_asignados_ordered" :value="sistema.id">{{ sistema.name }}</option>
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
                                   <label for="to">SISTEMAS DESHABILITADOS</label>
                                    <select multiple id="to" size="10" class="form-control" v-model="selected">
                                        <option v-for="sistema in sistemas_disponibles_ordered" :value="sistema.id">{{ sistema.name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                    <button class="btn btn-outline-success pull-right" :disabled="!sistemas_desasignados.length && !sistemas_nuevos_asignados.length" @click="validate"><i class="fa fa-save"></i></button>
                <div>
                 </div>
            </div>


        </div>
        <div class="modal" ref="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detalle de Habilitación/Deshabilitación</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table">
                                <tr v-if="sistemas_nuevos_asignados.length">
                                    <th>Sistemas a Habilitar:</th>
                                    <td>
                                        <ul>
                                            <li v-for="sistema in sistemas_nuevos_asignados">{{ sistema.name }}</li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr v-if="sistemas_desasignados.length">
                                    <th>Sistemas a Deshabilitar:</th>
                                    <td>
                                        <ul>
                                            <li v-for="sistema in sistemas_desasignados">{{ sistema.name }}</li>
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
    export default {
        name: "configuracion-sistema",

        data() {
            return {
                form: {
                    sistema_id: []
                },
                sistemas_disponibles: [],
                sistemas_asignados: [],
                sistemas_originales: [],
                sistemas_seleccionado: '',
                cargando: false,
                guardando: false,
                selected: []
            }
        },

        mounted() {
            this.getSistemas()
                .finally(() => {
                    this.getSistemasObra();
                })
        },
        methods: {

            getSistemas() {
                this.sistemas_disponibles = [];
                this.sistemas_originales = this.sistemas_originales.concat(this.sistemas_disponibles);
                return this.$store.dispatch('seguridad/sistema-obra/index')
                    .then(data => {
                        this.sistemas_disponibles = data.sort((a, b) => (a.name > b.name) ? 1 : -1);
                    });
            },
            getSistemasObra() {
                this.sistemas_originales = []
                this.sistemas_disponibles = this.sistemas_disponibles.concat(this.sistemas_asignados);
                return this.$store.dispatch('seguridad/sistema-obra/getSistemasObra')
                    .then(data => {
                        data.data.forEach(perm=> {
                            this.sistemas_originales.push(perm.id);
                        })

                        this.sistemas_asignados = data.data.sort((a, b) => (a.name > b.name) ? 1 : -1);
                        this.sistemas_disponibles = this.sistemas_disponibles.diff(this.sistemas_asignados);
                    });
            },
            agregar() {
                this.selected.forEach(permiso => {
                    this.sistemas_disponibles.forEach(r => {
                        if(r.id == permiso) {
                            this.sistemas_asignados.push(r)
                            this.sistemas_disponibles = this.sistemas_disponibles.filter(perm => {
                                return perm.id != r.id;
                            });
                        }
                    })
                })
            },

            quitar() {
                this.form.sistemas_id.forEach(sistema => {
                    this.sistemas_asignados.forEach(r => {
                        if(r.id == sistema) {
                            this.sistemas_disponibles.push(r)
                            this.sistemas_asignados = this.sistemas_asignados.filter(perm => {
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
                this.$store.dispatch('seguridad/sistema-obra/find', {
                    id: this.form.sistema_id
                })
                    .then(data => {
                        this.sistemas_seleccionado = data.display_name;
                        $(this.$refs.modal).modal('show');
                    })
            },
            guardar() {
                this.guardando = true;
                return this.$store.dispatch('seguridad/sistema-obra/asignarSistemas', {
                    sistema_id: this.sistemas_asignados.map(sistema => (
                        sistema.id
                    ))
                })
                    .then(data => {
                        this.sistemas_originales = this.sistemas_asignados.map(perm => (
                            perm.id
                        ))
                    } )
                    .finally(() => {
                        $(this.$refs.modal).modal('hide');
                        this.$validator.reset()
                        this.guardando = false;

                    });
            }
        },
        computed:{
            sistemas_asignados_ordered() {
                return this.sistemas_asignados.sort((a,b) => {
                    return (a.name<b.name?-1:(a.name>b.name?1:0));
                });
            },
            sistemas_disponibles_ordered() {
                return this.sistemas_disponibles.sort((a,b) => {
                    return (a.name<b.name?-1:(a.name>b.name?1:0));
                });
            },
            sistemas_desasignados() {
                return this.sistemas_disponibles.filter(sistemas => {
                    return $.inArray(sistemas.id, this.sistemas_originales) > -1;
                })
            },
            sistemas_nuevos_asignados() {
                return this.sistemas_asignados.filter(sistemas => {
                    return $.inArray(sistemas.id, this.sistemas_originales) == -1;
                })
            },
            currentObra() {
                return this.$store.getters['auth/currentObra']
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