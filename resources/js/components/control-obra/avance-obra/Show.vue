<template>
    <span>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <tabla-datos v-bind:id="id" />
                    </div>
                    <div class="card-footer">
                        <div class="pull-right">
                            <button type="button" class="btn btn-secondary" v-on:click="salir"><i class="fa fa-angle-left"></i>Regresar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import TablaDatos from './partials/TablaDatos'
    export default {
        name: "avance-obra-show",
        props: ['id'],
        components: {TablaDatos},
        methods: {
            salir() {
                this.$router.push({name: 'avance-obra'});
            },
        }
    }
</script>
