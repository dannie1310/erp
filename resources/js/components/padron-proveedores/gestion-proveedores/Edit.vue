<template>
    <span>
        <nav>
            <div class="card">
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label">Razón Social: </label>
                        <div class="col-md-6">
                            Proveedor S.A.
                        </div>
                        <label class="col-md-1 col-form-label">RFC: </label>
                        <div class="col-md-3">
                            PRTIUDASNSD
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <a aria-controls="nav-datos" aria-selected="true" class="nav-item nav-link active" data-toggle="tab" href="#nav-datos"
                   id="nav-datos-tab" role="tab">Datos Proveedor</a>
                <a aria-controls="nav-documentacion" aria-selected="false" class="nav-item nav-link" data-toggle="tab" href="#nav-documentacion"
                    id="nav-documentacion-tab" role="tab">Documentación General</a>
                <a aria-controls="nav-prestadora" aria-selected="false" class="nav-item nav-link" data-toggle="tab" href="#nav-prestadora"
                   id="nav-prestadora-tab" role="tab">Datos Prestadora de Servicios</a>
                <a aria-controls="nav-documentacion-prestadora" aria-selected="false" class="nav-item nav-link" data-toggle="tab" href="#nav-documentacion-prestadora"
                   id="nav-documentacion-prestadora-tab" role="tab">Documentación de Prestadora de Servicios</a>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <div aria-labelledby="nav-datos-tab" class="tab-pane fade show active" id="nav-datos" role="tabpanel">
                <DatosGenerales v-bind:id="id"></DatosGenerales>
            </div>
            <div aria-labelledby="nav-documentacion-tab" class="tab-pane fade" id="nav-documentacion" role="tabpanel">
                <TabDocumentacion v-bind:id="id"></TabDocumentacion>
            </div>
              <div aria-labelledby="nav-prestadora-tab" class="tab-pane fade" id="nav-prestadora" role="tabpanel">
                <DatosPrestadora></DatosPrestadora>
            </div>
            <div aria-labelledby="nav-documentacion-prestadora-tab" class="tab-pane fade" id="nav-documentacion-prestadora" role="tabpanel">
                <b>pandita</b>
            </div>
        </div>
    </span>
</template>

<script>
    import DatosGenerales from "./EditTabs/TabDatos";
    import TabDocumentacion from './EditTabs/TabDocumentacion';
    import DatosPrestadora from './EditTabs/TabDatosPrestadora'

    export default {
        name: "proveedores-edit",
        components: {DatosGenerales, DatosPrestadora,TabDocumentacion},
        props: ['id'],
        data() {
            return {
                cargando: false,
            }
        },
    }
</script>

<style>

</style>
