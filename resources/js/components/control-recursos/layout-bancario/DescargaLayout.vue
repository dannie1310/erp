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
                    <div class="col-md-12" v-if="idsemana && solicitudes">
                        <div class="form-group error-content">
                            <label for="idsolicitud" class="col-form-label">Solicitudes de Cheque:</label>
                            <select class="form-control"
                                    data-vv-as="Solicitud de Recurso"
                                    id="idsolicitud"
                                    name="idsolicitud"
                                    :error="errors.has('idsolicitud')"
                                    v-validate="{required: true}"
                                    v-model="idsolicitud">
                                <option value>-- Selecionar --</option>
                                <option v-for="(s) in solicitudes" :value="s.id">{{s.serie}} - {{s.numero}}</option>
                            </select>
                            <div style="display:block" class="invalid-feedback" v-show="errors.has('idsolicitud')">{{ errors.first('idsolicitud') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-12 pull-right">
                    <button type="button" class="btn btn-secondary" v-on:click="salir">
                        <i class="fa fa-angle-left"></i>Regresar
                    </button>
                    <button type="button" class="btn btn-primary" @click="descargar" :disabled="idsolicitud == '' ? true : false">
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
            idsemana: '',
            idsolicitud: '',
            solicitudes: []
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
            return this.$store.dispatch('controlRecursos/solicitud-cheque/descargar', {id: this.idsemana})
                .then(() => {
                    this.salir()
                })
        },
        getSolicitudes() {
            return this.$store.dispatch('controlRecursos/solicitud-cheque/index', {
                params: { scope:['porSemanaAnio:'+this.idsemana] }
            })
            .then(data => {
                this.solicitudes = data.data;
            })
        },
    },
    watch: {
        idsemana(value)
        {
            if(value)
            {
                this.getSolicitudes();
            }
        }
    }
}
</script>

<style scoped>

</style>
