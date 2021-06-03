<template>
    <span v-if="subcontrato">
        <encabezado-subcontrato v-bind:subcontrato="subcontrato"></encabezado-subcontrato>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered table-sm">
                    <tr >
                        <th class="encabezado">
                            Contratista
                        </th>
                        <th class="encabezado">
                            Folio
                        </th>
                        <th class="encabezado">
                            Referencia
                        </th>
                        <th class="encabezado">
                            Fecha
                        </th>
                    </tr>
                    <tr>
                        <td>
                            {{ subcontrato.empresa }}
                        </td>
                        <td>
                            {{subcontrato.numero_folio_format}}
                        </td>
                        <td>
                            {{ subcontrato.referencia }}
                        </td>
                        <td>
                            {{ subcontrato.fecha_format }}
                        </td>
                    </tr>
                </table>
                <div class="card" v-if="1==0">
                    <div class="card-header">
                        <h6 class="card-title">Información de Subcontrato</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Contratista:</label>
                            <div class="col-md-9">
                                {{ subcontrato.empresa }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">Folio:</label>
                                    <div class="col-md-9">
                                        {{subcontrato.numero_folio_format}} ({{ subcontrato.referencia }})
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">Fecha:</label>
                                    <div class="col-md-9">
                                        {{ subcontrato.fecha_format }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group row" >
                                    <label class="col-md-3 col-form-label">Monto:</label>
                                    <div class="col-md-9">
                                        {{ subcontrato.monto_format }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">IVA:</label>
                                    <div class="col-md-9">
                                        {{ subcontrato.impuesto_format }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group row">
                                    <label class="col-md-6 col-form-label">Moneda:</label>
                                    <div class="col-md-6">
                                        {{ subcontrato.moneda }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="btn-group pull-right">
                            <ShowSubcontrato v-bind:id="subcontrato.id" v-if="$root.can('consultar_subcontrato')"></ShowSubcontrato>
                            <PDFSubcontrato v-bind:id="subcontrato.id" @click="subcontrato.id" v-if="$root.can('consultar_subcontrato')"></PDFSubcontrato>
                            <router-link  :to="{ name: 'subcontrato-documentos', params: {id: subcontrato.id}}" target="_blank" v-if="$root.can('consultar_subcontrato') && $root.can('consultar_archivos_transaccion')" type="button" class="btn btn-sm btn-outline-primary" title="Ver Documentos">
                                <i class="fa fa-folder-open"></i>
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import ShowSubcontrato from '../Show';
import PDFSubcontrato from '../FormatoSubcontrato';
import EncabezadoSubcontrato from "../EncabezadoSubcontrato";
export default {
    name: "DatosSubcontrato",
    props: ['subcontrato'],
    components: {EncabezadoSubcontrato, ShowSubcontrato, PDFSubcontrato},
}
</script>

<style scoped>

</style>
