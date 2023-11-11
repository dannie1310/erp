<template>
    <span>
        <div class="card" v-if="relacion == null">
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
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <h4>Documento para Relación</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <encabezado v-bind:relacion="relacion" />
                        <resumen v-bind:relacion="relacion" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0" @click="validate"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-secondary" v-on:click="salir"><i class="fa fa-angle-left"></i>Regresar</button>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import Encabezado from './partials/Encabezado';
import Resumen from './partials/TablaDatosResumen';
export default {
    name: "SolicitaReembolsoXSolicitud",
    components: { Encabezado, Resumen},
    props: ['id'],
    data(){
        return{
            cargando: false,
            relacion : null,
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
                if (result)
                {
                    console.log(moment(this.relacion.fecha_final_editar).format('DD/MM/YYYY'))
                    console.log( moment(this.relacion.fecha_inicial_editar).format('DD/MM/YYYY'))
                    if(moment(this.relacion.fecha_final_editar).format('DD/MM/YYYY') < moment(this.relacion.fecha_inicial_editar).format('DD/MM/YYYY'))
                    {
                        swal('¡Error!', 'La fecha de final no puede ser posterior a la fecha de inicial.', 'error')
                    }
                    else {
                        this.store();
                    }
                }
            });
        },
        store() {
            return this.$store.dispatch('controlRecursos/relacion-gasto/reembolsoXSolicitud', this.relacion)
                .then((data) => {
                    this.relacion = data;
                    this.salir()
                });
        }
    },
}
</script>

<style scoped>

</style>
