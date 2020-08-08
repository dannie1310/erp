<template>
    <span>
        <nav>
            <div class="row">
                <div class="col-md-8">
                <div class="form-group">
                    <label><b>Razón Social:</b></label>
                    Proveedor S.A.
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label><b>RFC:</b></label>
                    ABC010101XYZ
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label><b>Registró Patronal del IMSS:</b></label>
                    1234567890
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label><b>Giro:</b></label>
                    Giro
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label><b>Especialidad:</b></label>
                    Especialidad
                </div>
            </div>
            </div>
            

            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <a aria-controls="nav-info" aria-selected="true" class="nav-item nav-link active" data-toggle="tab" href="#nav-info"
                       id="nav-info-tab" role="tab">Documentación General</a>
                    <a aria-controls="nav-profile" aria-selected="false" class="nav-item nav-link" data-toggle="tab"
                       href="#nav-profile" id="nav-profile-tab" role="tab">Documentación de Nómina</a>
                </div>
            </nav>

            <div class="tab-content" id="nav-tabContent">
                <div aria-labelledby="nav-info-tab" class="tab-pane fade show active" id="nav-info" role="tabpanel">
                    <TabGeneral v-bind:id="id"></TabGeneral>
                </div>
                <div aria-labelledby="nav-profile-tab" class="tab-pane fade" id="nav-profile" role="tabpanel">
                    <b>pandita</b>
                </div>
            </div>
        </nav>
    </span>
</template>

<script>
import TabGeneral from './EditTabs/TabGeneral';
export default {
    name: "proveedor-edit",
    components: {TabGeneral},
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