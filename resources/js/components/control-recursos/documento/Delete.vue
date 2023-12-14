<template>
    <span>
        <div class="card" v-if="!factura">
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
                            <encabezado v-bind:factura="factura" />
                            <tabla-datos v-bind:factura="factura" />
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
            factura : null
        }
    },
    mounted() {
        this.find();
    },
    methods: {
        find() {
            this.cargando = true;
            return this.$store.dispatch('controlRecursos/documento/find', {
                id: this.id,
                params:{include: []}
            }).then(data => {
                this.factura = data
            })
                .finally(()=> {
                    this.cargando = false;
                })
        },
        salir() {
            this.$router.go(-1);
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    if (this.factura.solicitado){
                        swal('¡Error!', 'El documento se encuentra solicitado, no se puede eliminar.', 'error')
                    }
                    else {
                        this.destroy();
                    }
                }
            });
        },
        destroy() {
            return this.$store.dispatch('controlRecursos/documento/delete', {
                id: this.id,
                params: {}
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
