<template>
    <span>
        <div class="row">
            <div class="col-md-12">
                <br>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="index_corto">#</th>
                                <th >Documento</th>
                                <th >Estatus</th>
                                <th >Nombre Archivo</th>
                                <th >Usuario Cargo</th>
                                <th >Fecha Hora Carga</th>
                                <th >Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <!-- <button type="button" class="btn btn-secondary pull-right"><i class="fa fa-angle-left"></i>Registrar</button> -->
                </div>
            </div>
        </div>
    </span>
</template>

<script>
export default {
    name: "proveedor-edit-tab-general",
    props: ['id'],
    data(){
        return{
        }
    },
    mounted() {
    },
    methods: {
    },
    computed: {
    }

}
</script>

<style>

</style>