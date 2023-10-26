<template>
    <span>
        <div class="card" v-if="!relacion">
            <div class="card-body">
                <div class="row" >
                    <div class="col-md-12">
                        <div class="spinner-border text-success" role="status">
                           <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card" v-else>
            <form role="form" @submit.prevent="validate">
                <div class="card-body">
                    <div class="row" >
                        <div class="col-md-12">
                            <encabezado v-bind:relacion="relacion" />
                            <tabla-datos v-bind:relacion="relacion" />
                        </div>
                    </div>
                     <hr />
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row error-content">
                                <label for="motivo" class="col-md-2 col-form-label">Motivo:</label>
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
                        <button type="submit" class="btn btn-danger" :disabled="errors.count() > 0 && motivo == null"><i class="fa fa-trash"></i>Eliminar</button>
                    </div>
                </div>
            </form>
        </div>
    </span>
</template>

<script>
import Encabezado from './partials/Encabezado';
import TablaDatos from "./partials/TablaDatos";
export default {
    components: { Encabezado, TablaDatos },
    name: "Delete",
    props: ['id'],
    data() {
        return {
            cargando: false,
            relacion : null,
            motivo: null
        }
    },
    mounted() {
        this.find();
    },
    methods: {
        find() {
            this.cargando = true;
            return this.$store.dispatch('controlRecursos/relacion-gasto/find', {
                id: this.id,
                params:{include: []}
            }).then(data => {
                this.relacion = data
            })
                .finally(()=> {
                    this.cargando = false;
                })
        },
        salir() {
            this.$router.push({name: 'relacion-gasto'});
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    this.destroy()
                }
            });
        },
        destroy() {
            return this.$store.dispatch('controlRecursos/relacion-gasto/delete', {
                id: this.id,
                params: {data: this.$data.motivo}
            }).then(() => {
                this.$store.commit('controlRecursos/relacion-gasto/DELETE_RELACION', {id: this.id})
                this.salir();
            })
        },
    }
}
</script>

<style scoped>

</style>
