<template>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="form-row">
                        <div class="col-md-12">
                            <div class="row">
                                <label for="listas">Imprimir Por:</label>
                                <select id="listas" name="listas" class="list">
                                    <option value="1">Usuario</option>
                                    <option value="2">Activo</option>
                                    <option value="3">Departamento</option>
                                    <option value="4">Ref. Factura</option>
                                </select>
                            </div>
                        </div>
                       <!-- <div class="col">
                            <div class="form-group error-content">
                                <label for="ubicaciones">Ubicación:</label>
                                <model-list-select
                                    :disabled="cargando"
                                    name="ubicaciones"
                                    v-model="ubicacion"
                                    option-value="idubicacion"
                                    option-text="Ubicacion"
                                    :list="usuariosUbicaciones"
                                    :placeholder="!cargando?'Seleccionar o buscar por ubicación':'Cargando...'"
                                    :isError="errors.has(`ubicaciones`)">
                                </model-list-select>
                                <div class="invalid-feedback" v-show="errors.has('ubicaciones')">{{ errors.first('ubicaciones') }}</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group row">
                                <label for="user_id">Empleado:</label>
                                <usuario-select
                                    name="user_id"
                                    id="user_id"
                                    data-vv-as="Usuario"
                                    v-validate="{ integer: true}"
                                    v-model="user_id"
                                    :error="errors.has('user_id')"
                                >
                                </usuario-select>
                                <div class="error-label" v-show="errors.has('user_id')">{{ errors.first('user_id') }}</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group error-content">
                                <label for="tipoActivo">Tipo de Activo:</label>
                                <model-list-select
                                    :disabled="cargando"
                                    name="tipoActivo"
                                    v-model="tipoActivo"
                                    option-value="idGrupo"
                                    option-text="Descripcion"
                                    :list="tiposActivo"
                                    :placeholder="!cargando?'Seleccionar o buscar tipo de activo por descripcion':'Cargando...'"
                                    :isError="errors.has(`tipoActivo`)">
                                </model-list-select>
                                <div class="invalid-feedback" v-show="errors.has('tipoActivo')">{{ errors.first('tipoActivo') }}</div>
                            </div>
                        </div>-->
                    </div>
                </div>
                <div class="card-footer">
                    <div class="pull-right">
                        <button type="button" class="btn btn-primary" v-on:click="getResguardos" :disabled="cargando || buscando">
                            <span v-if="!buscando">
                                <i class="fa fa-search"></i>Consultar
                            </span>
                            <span v-else>
                                <i class="fa fa-spin fa-spinner" ></i>Buscando...
                            </span>

                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12" v-if="activos.length > 0">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <datatable v-bind="$data" />
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
</template>

<script>
export default {
    name: "Index",
    listas: 0
}
</script>

<style scoped>

</style>
