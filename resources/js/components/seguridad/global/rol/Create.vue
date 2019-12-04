<template>
    <div class="card" id="seguridad-rol-create">
        <div class="card-header">
            <h6 class="card-title">
                Crear Rol
            </h6>
        </div>
        <div class="card-body">
            <div class="row" role="form">
                <div class="form-group col-md-6">
                    <label for="display_name">Nombre</label>
                    <input
                            type="text"
                            name="display_name"
                            data-vv-as="Nombre"
                            v-validate="{required: true, max: 255}"
                            class="form-control"
                            id="display_name"
                            v-model="form.display_name"
                            :class="{'is-invalid': errors.has('display_name')}"
                    >
                    <div class="invalid-feedback" v-show="errors.has('display_name')">{{ errors.first('display_name') }}</div>
                </div>

                <div class="form-group col-md-6">
                    <label for="description">Descripción</label>
                    <input
                            type="text"
                            name="description"
                            data-vv-as="Descripción"
                            v-validate="{required: true, max: 255}"
                            class="form-control"
                            id="description"
                            v-model="form.description"
                            :class="{'is-invalid': errors.has('description')}"
                    >
                    <div class="invalid-feedback" v-show="errors.has('description')">{{ errors.first('description') }}</div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive" style="max-height: 500px; overflow-y: scroll;">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th></th>
                            </tr>
                            <tr>
                                <th colspan="3">
                                    <input type="text" class="form-control" v-model="search" placeholder="--Buscar Permisos--">
                                </th>
                            </tr>
                            </thead>
                            <tbody v-for="grupo in permisos_agrupados">
                            <tr>
                                <td colspan="3" class="text-center"><h5>{{ grupo[0].sistema ? grupo[0].sistema.name : 'N/A' }}</h5></td>
                            </tr>
                            <tr v-for="permiso in grupo">
                                <td>{{ permiso.display_name }}</td>
                                <td>{{ permiso.description }}</td>
                                <td><input type="checkbox" :value="permiso.id" v-model="form.permission_id"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="form-group row" style="margin-top: 15px">
                <div class="col">
                    <button type="submit" @click="validate" class="btn btn-outline-primary float-right" :disabled="guardando">
                        <i class="fa fa-spin fa-spinner" v-if="guardando"></i>
                        <i class="fa fa-save" v-else></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "seguridad-rol-create",
        data() {
            return {
                form: {
                    display_name: '',
                    description: '',
                    permission_id: []
                },
                permisos: [],
                search: '',

                guardando: false
            }
        },

        mounted() {
            this.getPermisos();
        },

        methods: {
            getPermisos() {
                return this.$store.dispatch('seguridad/permiso/index', {
                    config: {
                        params: {
                            include: 'sistema'
                        }
                    }
                })
                    .then(data => {
                        this.permisos = data.data;
                    })
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.save()
                    }
                });
            },

            save() {
                this.guardando = true;
                return this.$store.dispatch('seguridad/rol/store', this.form)
                    .then(data => {
                            this.form.display_name = '';
                            this.form.description = '';
                            this.form.permission_id = [];

                            this.search = '';
                            this.$validator.reset()
                            this.$emit('creado', data);
                    })
                    .finally(() => {
                        this.guardando = false;
                    })
            }
        },

        computed: {
            permisos_agrupados() {
                let perms = this.permisos.filter(p => {
                    return p.display_name.toLowerCase().includes(this.search.toLowerCase()) || p.description.toLowerCase().includes(this.search.toLowerCase())
                });
                return _.groupBy(perms, 'sistema.url');
            }
        }
    }
</script>