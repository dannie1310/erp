<template>
    <div class="card">
        <div class="card-header">

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

            <div class="row">
                <div class="col-lg-4">
                    <div class="form form-group">
                        <label for="id_proyecto">{{ form.tipo_asignacion == 1 ? 'Proyectos' : 'Proyecto' }}</label>
                        <select size="10" class="form-control" multiple :disabled="!obras_agrupadas" v-model="form.id_proyecto">
                            <optgroup :label="i" v-for="(grupo, i) in obras_agrupadas">
                                <option v-for="obra in grupo" :value="`${i}-${obra.id_obra}`">{{ obra.nombre }}</option>
                            </optgroup>
                        </select>
                    </div>
                </div>
                <div class="col-lg-8">

                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import UsuarioSelect from "../../../igh/usuario/Select";
    export default {
        name: "asignacion-roles-masiva",
        components: {UsuarioSelect},
        data() {
            return {
                form: {
                    user_id: '',
                    id_proyecto: []
                },
                obras_agrupadas: null,
                cargando: false
            }
        },

        methods: {
            getObrasPorUsuario(id) {
                return this.$store.dispatch('cadeco/obras/getObrasPorUsuario', {
                    user_id: id
                });
            }
        },

        watch: {
            'form.user_id'(id) {
                this.form.id_proyecto = [];
                this.obras_agrupadas = null;

                if (id) {
                    this.cargando = true;
                    this.getObrasPorUsuario(id)
                        .then(data => {
                            this.obras_agrupadas = _.groupBy(data.data, 'base_datos');
                        })
                        .finally(() => {
                            this.cargando = false;
                        })
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
</style>