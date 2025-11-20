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
                        <th class="encabezado" colspan="2">
                            Proveedor
                        </th>
                        <th class="encabezado" colspan="2" v-if="factura.id_proveedor_emisor != NULL">
                            Proveedor Emisor
                        </th>
                    </tr>
                    <tr>
                        <th class="encabezado" colspan="2">
                            {{factura.empresa_descripcion}}
                        </th>
                        <th class="encabezado" colspan="2">
                            {{factura.proveedor_descripcion}}
                        </th>
                        <th class="encabezado" colspan="2" v-if="factura.id_proveedor_emisor != NULL">
                            {{factura.proveedor_emisor_descripcion}}
                        </th>
                    </tr>
                    <tr>
                        <th class="encabezado">
                           Tipo de Documento
                        </th>
                        <th class="encabezado">
                            Fecha de Emisión
                        </th>
                        <th class="encabezado">
                            Fecha de Vencimiento
                        </th>
                        <th class="encabezado">
                            Serie
                        </th>
                        <th class="encabezado">
                            Folio
                        </th>
                        <th class="encabezado money">
                            Tipo de Cambio
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
                        <td class="money">
                            {{ factura.tc }}
                        </td>
                    </tr>
                    <tr>
                        <th class="encabezado" colspan="6">Concepto</th>
                    </tr>
                    <tr>
                        <td colspan="6">
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
                            Tasa de Iva
                        </th>
                        <th class="encabezado money">
                            Descuento
                        </th>
                        <th class="encabezado money">
                            Retenciones
                        </th>
                        <th class="encabezado">
                            Otros Impuestos
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
                            {{ factura.descuento }}
                        </td>
                        <td class="money">
                            {{ factura.retencion_format }}
                        </td>
                        <td class="money">
                            {{ factura.otros_format }}
                        </td>
                    </tr>
                    <tr>
                        <th class="encabezado">
                            Total
                        </th>
                        <th class="encabezado">
                            Moneda
                        </th>
                        <th class="encabezado">
                            UUID
                        </th>
                        <th class="encabezado">
                            Ubicación
                        </th>
                        <th class="encabezado">
                            Alias del Departamento
                        </th>
                        <th class="encabezado">
                            Departamento
                        </th>

                    </tr>
                    <tr>
                        <td class="money">
                            {{ factura.total_format }}
                        </td>
                        <td>
                            {{ factura.moneda }}
                        </td>
                        <td>
                            {{factura.uuid}}
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
