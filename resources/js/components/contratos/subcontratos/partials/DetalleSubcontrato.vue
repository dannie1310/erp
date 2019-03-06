<template>
    <span>
        <div class="card">
            <div class="card-header">
                <div class="row" >
                    <div class="col-md-12">
                        <label ><i class="fa fa-info-circle" style="padding-right:3px"></i>Subcontrato {{subcontrato.numero_folio_format}} [{{subcontrato.referencia}}]</label>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <label >Referencia:</label>
                    </div>
                    <div class="col-md-6">
                        {{subcontrato.referencia}}
                    </div>
                    <!-- Fecha -->
                    <div class="col-md-2 money" >
                        <label>Fecha:</label>
                    </div>
                    <div class="col-md-2 money">
                        {{ subcontrato.fecha_format}}
                    </div>
                </div>
                <div class="row">
                    <!-- Observaciones -->
                    <div class="col-md-2">
                        <label >Empresa:</label>
                    </div>
                    <div class="col-md-10">
                        {{subcontrato.empresa.razon_social}}
                    </div>
                </div>
                <div class="row">
                    <!-- Subtotal -->
                    <div class="col-md-2">
                        <label>Subtotal:</label>
                    </div>
                    <div class="col-md-2 money">
                        {{ subcontrato.subtotal_format}}
                    </div>
                    <!-- Impuesto -->
                    <div class="col-md-2">
                        <label>Impuesto:</label>
                    </div>
                    <div class="col-md-2 money">
                        {{subcontrato.impuesto_format }}
                    </div>
                    <!-- Monto -->
                    <div class="col-md-2 money">
                        <label>Total: </label>
                    </div>
                    <div class="col-md-2 money">
                        {{subcontrato.monto_format}}
                    </div>
                </div>
                <div class="row">
                    <!-- Moneda -->
                    <div class="col-md-2">
                        <label>Moneda:</label>
                    </div>
                    <div class="col-md-3 ">
                        {{subcontrato.moneda.nombre}}
                    </div>
                    <!-- % Anticipo -->
                    <div class="col-md-1">
                        <label>Anticipo:</label>
                    </div>
                    <div class="col-md-1 money">

                        {{subcontrato.anticipo}} %
                    </div>
                    <!-- % Fondo e Garantía -->
                    <div class="col-md-3 money">
                        <label>Fondo de Garantía: </label>
                    </div>
                    <div class="col-md-2 money">
                        {{subcontrato.retencion}} %
                    </div>
                </div>
                <div class="row">
                    <!-- Observaciones -->
                    <div class="col-md-2">
                        <label for="id_subcontrato">Observaciones:</label>
                    </div>
                    <div class="col-md-10">
                        {{subcontrato.observaciones}}
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "detalle-subcontrato",
        props: {
            subcontrato : {
                type: Object,
                default: () => ({})
            }},
    }
</script>

<style scoped>

</style>