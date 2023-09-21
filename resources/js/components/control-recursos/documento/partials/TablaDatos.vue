<template>
    <div class="row" v-if="factura">
        <div class="col-md-12">
            <span><i class="fa fa-file-invoice"></i>Datos del Documento</span>
            <div class="table-responsive">
                <table class="table  table-sm">
                    <tr>
                        <th class="encabezado" colspan="2">
                            Empresa
                        </th>
                        <th class="encabezado" colspan="3">
                            Proveedor
                        </th>
                    </tr>
                    <tr>
                        <th class="encabezado" colspan="2">
                            {{factura.empresa_descripcion}}
                        </th>
                        <th class="encabezado" colspan="3">
                            {{factura.proveedor_descripcion}}
                        </th>
                    </tr>
                    <tr>
                        <th class="encabezado  c130">
                           Tipo de Documento
                        </th>
                        <th class="encabezado  c130">
                            Fecha de Emisión
                        </th>
                        <th class="encabezado  c130">
                            Fecha de Vencimiento
                        </th>
                        <th class="encabezado">
                            Serie
                        </th>
                        <th class="encabezado">
                            Folio
                        </th>

                    </tr>
                    <tr>
                        <td style="text-align: center">
                            {{ factura.tipo_documento }}
                        </td>
                        <td style="text-align: center">
                            {{ factura.fecha_format }}
                        </td>
                        <td style="text-align: center">
                            {{ factura.fecha_vencimiento_format }}
                        </td>
                        <td>
                            {{factura.serie}}
                        </td>
                        <td>
                            {{factura.folio_format}}
                        </td>
                    </tr>
                    <tr>
                        <th class="encabezado" colspan="5">Concepto</th>
                    </tr>
                    <tr>
                        <td colspan="5">
                            {{ factura.concepto }}
                        </td>
                    </tr>
                    <tr>
                        <th class="encabezado">
                            Subtotal
                        </th>
                        <th class="encabezado">
                            Impuesto
                        </th>
                        <th class="encabezado">
                            Tasa de IVA
                        </th>
                        <th class="encabezado money">
                            Retenciones
                        </th>
                        <th class="encabezado">
                            Total
                        </th>
                    </tr>
                    <tr>
                        <td class="money">
                            {{ factura.importe_format}}
                        </td>
                        <td class="money">
                            {{ factura.iva_format }}
                        </td>
                        <td class="money">
                            {{ factura.tasa_iva }}
                        </td>
                        <td class="money">
                            {{ factura.retencion_format }}
                        </td>
                        <td class="money">
                            {{ factura.total_format }}
                        </td>
                    </tr>
                    <tr>
                        <th class="encabezado">
                            Moneda
                        </th>
                        <th class="encabezado money">
                            Tipo de Cambio
                        </th>
                        <th class="encabezado c150">
                            Ubicación
                        </th>
                        <th class="encabezado c150">
                            Alias del Departamento
                        </th>
                        <th class="encabezado c150">
                            Departamento
                        </th>

                    </tr>
                    <tr>
                        <td>
                            {{ factura.moneda }}
                        </td>
                        <td class="money">
                            {{ factura.tc }}
                        </td>
                        <td >
                            {{factura.ubicacion}}
                        </td>
                        <td >
                            {{factura.alias_dep}}
                        </td>
                        <td >
                            {{factura.departamento}}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "FacturaTablaDatos",
    components: { },
    props: ['factura'],
    methods :{

    }
}
</script>

<style scoped>
.encabezado{
    text-align: center; background-color: #f2f4f5
}
td, th{
    border: 1px #dee2e6 solid;
}

</style>
