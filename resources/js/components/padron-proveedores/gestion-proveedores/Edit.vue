<template>
    <span>
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <a aria-controls="nav-home" aria-selected="true" class="nav-item nav-link active" data-toggle="tab" href="#nav-home"
                   id="nav-home-tab" role="tab">Datos Proveedor</a><!-- Perfilar  -->
                <a aria-controls="nav-profile" aria-selected="false" class="nav-item nav-link" data-toggle="tab"
                   href="#nav-profile" id="nav-profile-tab" role="tab">Datos Prestadora de Servicios</a>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <div aria-labelledby="nav-home-tab" class="tab-pane fade show active" id="nav-home" role="tabpanel">
                <Proveedor></Proveedor>
            </div>
            <div aria-labelledby="nav-profile-tab" class="tab-pane fade" id="nav-profile" role="tabpanel">
                <EmpresaPrestadora></EmpresaPrestadora>
            </div>
        </div>
    </span>
</template>

<script>
    import Proveedor from "./proveedor/Edit";
    import EmpresaPrestadora from './empresa-prestadora/Edit'
    export default {
        name: "gestion-proveedor-edit",
        components: {Proveedor, EmpresaPrestadora},
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
