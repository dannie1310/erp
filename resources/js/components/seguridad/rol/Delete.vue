<template>
    <span>
        <div class="card">
            <div class="card-header">
                <h6 class="card-title">Eliminar Roles</h6>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <rol-select ref="rolSelect" v-model="role_id"></rol-select>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-danger" @click="eliminar" :disabled="!role_id">
                            <i class="fa fa-spin fa-spinner" v-if="eliminando"></i>
                            <i class="fa fa-trash" v-else></i>
                        </button>
                    </div>
                </div>
            </div>
            <!-- /.card-body -->
        </div>
    </span>
</template>

<script>
    import RolSelect from "../global/rol/Select";
    export default {
        name: "rol-delete",
        components: {RolSelect},
        data() {
            return {
                role_id: '',
                eliminando: false
            }
        },

        methods: {
            eliminar() {
                this.eliminando = true;
                return this.$store.dispatch('seguridad/rol/delete', this.role_id)
                    .then(() => {
                        this.role_id = '';
                    })
                    .finally(() => {
                        this.eliminando = false;
                    })
            }
        }
    }
</script>