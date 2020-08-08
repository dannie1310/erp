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
            </div>
        </nav>
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <a aria-controls="nav-home" aria-selected="true" class="nav-item nav-link active" data-toggle="tab" href="#nav-home"
                   id="nav-home-tab" role="tab">Datos Proveedor</a><!-- Perfilar  -->
                <a aria-controls="nav-profile" aria-selected="false" class="nav-item nav-link" data-toggle="tab"
                   href="#nav-profile" id="nav-profile-tab" role="tab">Datos Prestadora de Servicios</a>
                 <a aria-controls="nav-info" aria-selected="true" class="nav-item nav-link active" data-toggle="tab" href="#nav-info"
                    id="nav-info-tab" role="tab">Documentación General</a>
                    <a aria-controls="nav-profile" aria-selected="false" class="nav-item nav-link" data-toggle="tab"
                       href="#nav-profile" id="nav-profile-tab" role="tab">Documentación de Nómina</a>

            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <div aria-labelledby="nav-home-tab" class="tab-pane fade show active" id="nav-home" role="tabpanel">
                <Proveedor></Proveedor>
            </div>
            <div aria-labelledby="nav-profile-tab" class="tab-pane fade" id="nav-profile" role="tabpanel">
                <EmpresaPrestadora></EmpresaPrestadora>
            </div>
             <div aria-labelledby="nav-info-tab" class="tab-pane fade show active" id="nav-info" role="tabpanel">
                    <TabGeneral v-bind:id="id"></TabGeneral>
                </div>
                <div aria-labelledby="nav-profile-tab" class="tab-pane fade" id="nav-profile" role="tabpanel">
                    <b>pandita</b>
                </div>
        </div>
    </span>
</template>

<script>
    import Proveedor from "./proveedor/Edit";
    import EmpresaPrestadora from './empresa-prestadora/Edit'
    import TabGeneral from './EditTabs/TabGeneral';
    export default {
        name: "proveedor-edit",
        components: {Proveedor, EmpresaPrestadora,TabGeneral},
        props: ['id'],
        data() {
            return {
                es: es,
                cargando: false,
            }
        },
    }
</script>

<style>

</style>
