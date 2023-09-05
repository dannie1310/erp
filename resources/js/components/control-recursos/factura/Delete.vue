<template>
    <span>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <form role="form" @submit.prevent="validate">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <show v-bind:id="id" />
                                </div>
                            </div>
                            <br />
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="motivo" class="col-md-2 col-form-label">Motivo de eliminación:</label>
                                        <div class="col-md-10">
                                            <textarea
                                                name="motivo"
                                                id="motivo"
                                                class="form-control"
                                                v-model="motivo"
                                                v-validate="{required: true}"
                                                data-vv-as="Motivo"
                                                :class="{'is-invalid': errors.has('motivo')}"
                                            ></textarea>
                                            <div class="invalid-feedback" v-show="errors.has('motivo')">{{ errors.first('motivo') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="pull-right">
                                <button type="button" class="btn btn-secondary" v-on:click="salir"><i class="fa fa-angle-left"></i>Regresar</button>
                                <button type="submit" class="btn btn-danger" :disabled="errors.count() > 0"><i class="fa fa-trash"></i>Eliminar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import Show from "./Show"
export default {
    components: {Show},
    name: "Delete",
    props: ['id'],
    data() {
        return {
            cargando: false,
            motivo: ''
        }
    },
    methods: {
        salir() {
            this.$router.go(-1);
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    this.destroy()
                }
            });
        },
        destroy() {
            return this.$store.dispatch('controlRecursos/documento/delete', {
                id: this.id,
                params: {data: this.motivo}
            }).then(() => {
                this.$store.commit('controlRecursos/documento/DELETE_DOCUMENTO', {id: this.id})
                this.salir();
            })
        },
    }
}
</script>

<style scoped>

</style>
