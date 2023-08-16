<template>
    <span>
        <div class="card" v-if="cargando">
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
            <div class="card-header">
                <div class="col-12">
                    <h4>
                        <i class="fa fa-file-code-o"></i> Descargar Layout Bancario
                    </h4>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group error-content">
                            <label for="idsemana" class="col-form-label">Año y semana:</label>
                            <select class="form-control"
                                    data-vv-as="Semana y año"
                                    id="idsemana"
                                    name="idsemana"
                                    :error="errors.has('idsemana')"
                                    v-validate="{required: true}"
                                    v-model="idsemana">
                                <option value>-- Selecionar --</option>
                                <option v-for="(s) in semanas" :value="s.id">Año: {{s.anio}}    Semana: {{ s.semana }}</option>
                            </select>
                            <div style="display:block" class="invalid-feedback" v-show="errors.has('idsemana')">{{ errors.first('idsemana') }}</div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <button type="button" class="btn btn-secondary  float-right" v-on:click="salir">
                        <i class="fa fa-angle-left"></i>Regresar
                    </button>
                    <button type="button" class="btn btn-primary float-right" @click="descargar" :disabled="idsemana == '' ? true : false">
                        <i class="fa fa-download"></i>Descargar
                    </button>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
export default {
    name: "DescargaLayout",
    data() {
        return {
            cargando: false,
            semanas: [],
            idsemana: ''
        }
    },
    mounted() {
        this.$validator.reset()
        this.getSemanas();
    },
    methods : {
        getSemanas() {
            this.cargando = true;
            return this.$store.dispatch('controlRecursos/semana-anio/index', {
                params: { scope:'ordenarPorSemana' }
            })
                .then(data => {
                    this.semanas = data.data;
                })
                .finally(() => {
                    this.cargando = false;
                })
        },
        salir()
        {
            this.$router.go(-1);
        },
        descargar()
        {
            return this.$store.dispatch('controlRecursos/solicitud-recurso/descargar', {id: this.idsemana})
                .then(() => {
                    this.salir()
                })
        }
    }
}
</script>

<style scoped>

</style>
