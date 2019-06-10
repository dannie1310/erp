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
                                    <button class="btn col-xs-12 btn-default" title="Agregar" ><i class="fa fa-long-arrow-left"></i></button>
                                    <button class="btn col-xs-12 btn-default" title="Quitar" ><i class="fa fa-long-arrow-right"></i></button>
                                </div>
                            </div>

                            <div class="col-sm-5">
                                <div class="form-group">
                                   <label for="to">SISTEMAS DISPONIBLES</label>
                                    <select multiple id="to" size="10" class="form-control" v-model="selected">
                                        <option v-for="sistema in sistemas_disponibles_ordered" :value="sistema.id">{{ sistema.name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
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
                                    <td></td>
                                </tr>
                                <tr>
                                    <th>Permisos a Asignar:</th>
                                    <td>
                                        <ul>
                                            <li ></li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr >
                                    <th>Permisos a Desasignar:</th>
                                    <td>
                                        <ul>
                                            <li </li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" >
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
                // rol_seleccionado: '',
                cargando: false,
                guardando: false,
                selected: []
            }
        },

        mounted() {
            this.getSistemas();
        },
        methods: {

            getSistemas() {
                this.sistemas_disponibles = [];
                return this.$store.dispatch('seguridad/sistema-obra/index')
                    .then(data => {
                        this.sistemas_disponibles = data.sort((a, b) => (a.name > b.name) ? 1 : -1);
                    });
            },
            getSistemasObra() {
                this.sistemas_originales = []
                this.sistemas_disponibles = this.sistemas_disponibles.concat(this.sistemas_asignados);
                return this.$store.dispatch('seguridad/sistema-obra/getSistemasObras')
                    .then(data => {
                        data.data.forEach(perm=> {
                            this.sistemas_originales.push(perm.id);
                        })

                        this.sistemas_asignados = data.data.sort((a, b) => (a.name > b.name) ? 1 : -1);
                        this.sistemas_disponibles = this.sistemas_disponibles.diff(this.sistemas_asignados);
                    });
            },
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
            currentObra() {
                return this.$store.getters['auth/currentObra']
            }
        },
        watch: {
            'form.user_id'(id) {
                this.$validator.reset()
                if (id) {
                    this.cargando = true;
                    this.getSistemasObra(id)
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