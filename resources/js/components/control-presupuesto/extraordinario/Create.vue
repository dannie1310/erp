<template>
    <span>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <label>
                            1.-Indique si el concepto extraordinario pertenecerá al Costo Directo o al Costo Indirecto:
                        </label>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col-md-12">

                        <div class="btn-group btn-group-toggle">
                            <label class="btn btn-outline-secondary" :class="tipo_costo === Number(llave) ? 'active': ''" v-for="(tipo, llave) in tipos_costo" :key="llave">
                                <i :class="llave==1 ?'fa fa-building':'fa fa-boxes'"></i>
                                <input type="radio"
                                       class="btn-group-toggle"
                                       name="id_tipo"
                                       :id="'tipo' + llave"
                                       :value="llave"
                                       autocomplete="on"
                                       v-model.number="tipo_costo">
                                        {{ tipo}}
                            </label>
                        </div>
                    </div>
                </div>

                <span v-if="tipo_costo !=''">

                    <hr style="border-color: #009a43 ">

                <div class="row"  >
                    <div class="col-md-12">
                        <label>
                            2.-Ingrese los datos del concepto:
                        </label>
                    </div>
                </div>
                <br />

                <div class="row" >
                    <div class="col-md-12">
                        <table class="table table-sm">
                            <tr >
                                <th class="encabezado" colspan="2">
                                    Descripción
                                </th>
                                <th class="encabezado cantidad_input">
                                    Unidad
                                </th>
                                <th class="encabezado cantidad_input">
                                    Cantidad
                                </th>
                                <th class="encabezado cantidad_input">
                                    Precio Unitario
                                </th>
                                <th class="encabezado cantidad_input" colspan="2">
                                    Monto Presupuestado
                                </th>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <input type="text"
                                           class="form-control"
                                           name="descripcion"
                                           data-vv-as="Descripcion del Concepto"
                                           v-model="descripcion"
                                           v-validate="{required: true}"
                                           :class="{'is-invalid': errors.has('descripcion')}"
                                           id="descripcion">
                                    <div class="invalid-feedback" v-show="errors.has('descripcion')">{{ errors.first('descripcion') }}</div>
                                </td>
                                <td style="text-align: center">
                                    <model-list-select
                                        :disabled="cargando"
                                        name="id_unidad"
                                        id="id_unidad"
                                        v-model="unidad"
                                        option-value="unidad"
                                        option-text="unidad"
                                        v-validate="{required: true}"
                                        :list="unidades"
                                        :placeholder="!cargando?'Seleccionar o buscar':'Cargando...'"
                                        :isError="errors.has(`id_unidad`)">
                                    </model-list-select>
                                    <div class="invalid-feedback" v-show="errors.has('id_unidad')">{{ errors.first('id_unidad') }}</div>
                                </td>
                                <td style="text-align: center">
                                    <input type="text"
                                           class="form-control"
                                           name="cantidad"
                                           data-vv-as="Cantidad"
                                           v-model="cantidad"
                                           v-validate="{required: true, regex: /^[0-9]\d*(\.\d+)?$/}"
                                           :class="{'is-invalid': errors.has('cantidad')}"
                                           style="text-align: right"
                                           id="cantidad">
                                    <div class="invalid-feedback" v-show="errors.has('cantidad')">{{ errors.first('cantidad') }}</div>
                                </td>
                                <td style="text-align: right">
                                    ${{precio_unitario.formatMoney(2)}}
                                </td>
                                <td style="text-align: right" colspan="2">
                                    ${{monto_presupuestado.formatMoney(2)}}
                                </td>
                            </tr>
                            <tr>
                                <td style="border:none" colspan="7">
                                    <hr style="border-color: #009a43 ">
                                </td>
                            </tr>
                            <tr>
                                <td style="border:none" colspan="7">
                                    <label>
                                        3.-Agregue los insumos deseados
                                    </label>
                                </td>
                            </tr>
                            <template v-if="tipo_costo==1">
                                <!--MATERIALES -->
                                <tr>
                                    <td colspan="5" style="border:none"><h6><i class="fa fa-simplybuilt"/>Materiales</h6></td>
                                    <td colspan="2" style="border:none"></td>
                                </tr>
                                <tr >
                                    <th class="encabezado icono">
                                        #
                                    </th>
                                    <th class="encabezado">
                                        Descripción
                                    </th>
                                    <th class="encabezado cantidad_input">
                                        Unidad
                                    </th>
                                    <th class="encabezado cantidad_input">
                                        Cantidad
                                    </th>
                                    <th class="encabezado cantidad_input">
                                        Precio Unitario
                                    </th>
                                    <th class="encabezado cantidad_input">
                                        Importe
                                    </th>
                                    <th class="encabezado icono">
                                        <button type="button" class="btn btn-success btn-sm"   :title="cargando?'Cargando...':'Agregar Partidas'" :disabled="cargando" @click="addPartidaMaterial()">
                                            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                            <i class="fa fa-plus" v-else></i>
                                        </button>
                                    </th>
                                </tr>
                                <tr v-for="(partida_material, i) in partidas_material">
                                    <td style="text-align: center">
                                        {{i+1}}
                                    </td>
                                    <td >
                                        <span v-if="partida_material.material === ''">
                                            <MaterialSelect
                                                :name="`material[${i}]`"
                                                :scope="['materiales']"
                                                sort = "descripcion"
                                                v-model="partida_material.material"
                                                data-vv-as="Material"
                                                v-validate="{required: true}"
                                                :placeholder="!cargando?'Seleccionar o buscar material por descripcion':'Cargando...'"
                                                :class="{'is-invalid': errors.has(`material[${i}]`)}"
                                                ref="MaterialSelect"
                                                :disableBranchNodes="false"/>
                                            <div class="invalid-feedback" v-show="errors.has(`material[${i}]`)">{{ errors.first(`material[${i}]`) }}</div>
                                        </span>
                                        <span v-else>
                                            {{partida_material.material.descripcion}}
                                        </span>
                                    </td>
                                    <td >
                                        {{partida_material.material.unidad}}
                                    </td>
                                    <td >
                                        <input type="text"
                                               v-on:keyup="calcular"
                                               class="form-control"
                                               :name="`cantidad_material[${i}]`"
                                               :data-vv-as="`Cantidad Material ${i+1}`"
                                               v-model="partida_material.cantidad"
                                               v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d+)?$/}"
                                               :class="{'is-invalid': errors.has(`cantidad_material[${i}]`)}"
                                               :id="`cantidad_material[${i}]`"
                                               style="text-align: right"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has(`cantidad_material[${i}]`)">{{ errors.first(`cantidad_material[${i}]`) }}</div>
                                    </td>
                                    <td >
                                        <input type="text"
                                               v-on:keyup="calcular"
                                               class="form-control"
                                               :name="`precio_unitario[${i}]`"
                                               :data-vv-as="`Precio Unitario ${i+1}`"
                                               v-model="partida_material.precio_unitario"
                                               v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d+)?$/}"
                                               :class="{'is-invalid': errors.has(`precio_unitario[${i}]`)}"
                                               :id="`precio_unitario[${i}]`"
                                               style="text-align: right"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has(`precio_unitario[${i}]`)">{{ errors.first(`precio_unitario[${i}]`) }}</div>

                                    </td>
                                    <td style="text-align: right">
                                        ${{partida_material.importe.formatMoney(2)}}
                                    </td>
                                    <td >
                                        <button  type="button" class="btn btn-outline-danger btn-sm" @click="eliminaPartidaMaterial(i)"  ><i class="fa fa-trash"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" style="text-align: right; border: none">Suma de Partidas de Material:</td>
                                    <td style="text-align: right; border: none">${{suma_partidas_material.formatMoney(2)}}</td>
                                    <td style="border: none"></td>
                                </tr>
                                <tr>
                                    <td style="border: none" colspan="7">
                                        &nbsp;
                                    </td>
                                </tr>
                                <!--MANO DE OBRA -->
                                <tr>
                                    <td colspan="5" style="border:none"><h6><i class="fa fa-hand-paper"/>Mano de Obra</h6></td>
                                    <td colspan="2" style="border:none"></td>
                                </tr>

                                <tr >
                                    <th class="encabezado icono">
                                        #
                                    </th>
                                    <th class="encabezado">
                                        Descripción
                                    </th>
                                    <th class="encabezado cantidad_input">
                                        Unidad
                                    </th>
                                    <th class="encabezado cantidad_input">
                                        Cantidad
                                    </th>
                                    <th class="encabezado cantidad_input">
                                        Precio Unitario
                                    </th>
                                    <th class="encabezado cantidad_input">
                                        Importe
                                    </th>
                                    <th class="encabezado icono">
                                        <button type="button" class="btn btn-success btn-sm"   :title="cargando?'Cargando...':'Agregar Partidas'" :disabled="cargando" @click="addPartidaMO()">
                                            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                            <i class="fa fa-plus" v-else></i>
                                        </button>
                                    </th>
                                </tr>
                                <tr v-for="(partida_mo, i) in partidas_mo">
                                    <td style="text-align: center">
                                        {{i+1}}
                                    </td>
                                    <td >
                                        <span v-if="partida_mo.material === ''">
                                            <MaterialSelect
                                                :name="`mano_obra[${i}]`"
                                                :scope="['manoObra']"
                                                sort = "descripcion"
                                                v-model="partida_mo.material"
                                                data-vv-as="Material"
                                                v-validate="{required: true}"
                                                :placeholder="!cargando?'Seleccionar o buscar insumo por descripcion':'Cargando...'"
                                                :class="{'is-invalid': errors.has(`mano_obra[${i}]`)}"
                                                :ref="`MOSelect_${i}`"
                                                :disableBranchNodes="false"/>
                                            <div class="invalid-feedback" v-show="errors.has(`mano_obra[${i}]`)">{{ errors.first(`mano_obra[${i}]`) }}</div>
                                        </span>
                                        <span v-else>
                                            {{partida_mo.material.descripcion}}
                                        </span>
                                    </td>
                                    <td >
                                        {{partida_mo.material.unidad}}
                                    </td>
                                    <td >
                                        <input type="text"
                                               v-on:keyup="calcularMO"
                                               class="form-control"
                                               :name="`cantidad_mo[${i}]`"
                                               :data-vv-as="`Cantidad Material ${i+1}`"
                                               v-model="partida_mo.cantidad"
                                               v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d+)?$/}"
                                               :class="{'is-invalid': errors.has(`cantidad_mo[${i}]`)}"
                                               :id="`cantidad_mo_${i}`"
                                               style="text-align: right"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has(`cantidad_mo[${i}]`)">{{ errors.first(`cantidad_mo[${i}]`) }}</div>
                                    </td>
                                    <td >
                                        <input type="text"
                                               v-on:keyup="calcularMO"
                                               class="form-control"
                                               :name="`precio_unitario_mo[${i}]`"
                                               :data-vv-as="`Precio Unitario ${i+1}`"
                                               v-model="partida_mo.precio_unitario"
                                               v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d+)?$/}"
                                               :class="{'is-invalid': errors.has(`precio_unitario_mo[${i}]`)}"
                                               :id="`precio_unitario_mo[${i}]`"
                                               style="text-align: right"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has(`precio_unitario_mo[${i}]`)">{{ errors.first(`precio_unitario_mo[${i}]`) }}</div>

                                    </td>
                                    <td style="text-align: right">
                                        ${{partida_mo.importe.formatMoney(2)}}
                                    </td>
                                    <td >
                                        <button  type="button" class="btn btn-outline-danger btn-sm" @click="eliminaPartidaMO(i)"  ><i class="fa fa-trash"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" style="text-align: right; border: none">Suma de Partidas de Mano de Obra:</td>
                                    <td style="text-align: right; border: none">${{suma_partidas_mo.formatMoney(2)}}</td>
                                    <td style="border: none"></td>
                                </tr>
                                <tr>
                                    <td style="border: none">&nbsp;</td>
                                </tr>

                                <!-- HERRAMIENTA Y EQUIPO -->
                                <tr>
                                    <td colspan="5" style="border:none"><h6><i class="fa fa-tools"/>Herramienta y Equipo</h6></td>
                                    <td colspan="2" style="border:none"></td>
                                </tr>

                                <tr >
                                    <th class="encabezado icono">
                                        #
                                    </th>
                                    <th class="encabezado">
                                        Descripción
                                    </th>
                                    <th class="encabezado cantidad_input">
                                        Unidad
                                    </th>
                                    <th class="encabezado cantidad_input">
                                        Cantidad
                                    </th>
                                    <th class="encabezado cantidad_input">
                                        Precio Unitario
                                    </th>
                                    <th class="encabezado cantidad_input">
                                        Importe
                                    </th>
                                    <th class="encabezado icono">
                                        <button type="button" class="btn btn-success btn-sm"   :title="cargando?'Cargando...':'Agregar Partidas'" :disabled="cargando" @click="addPartidaHE()">
                                            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                            <i class="fa fa-plus" v-else></i>
                                        </button>
                                    </th>
                                </tr>
                                <tr v-for="(partida_he, i) in partidas_he">
                                    <td style="text-align: center">
                                        {{i+1}}
                                    </td>
                                    <td >
                                        <span v-if="partida_he.material === ''">
                                            <MaterialSelect
                                                :name="`herramienta[${i}]`"
                                                :scope="['herramientas']"
                                                sort = "descripcion"
                                                v-model="partida_he.material"
                                                data-vv-as="Material"
                                                v-validate="{required: true}"
                                                :placeholder="!cargando?'Seleccionar o buscar insumo por descripcion':'Cargando...'"
                                                :class="{'is-invalid': errors.has(`herramienta[${i}]`)}"
                                                ref="HESelect"
                                                :disableBranchNodes="false"/>
                                            <div class="invalid-feedback" v-show="errors.has(`herramienta[${i}]`)">{{ errors.first(`herramienta[${i}]`) }}</div>
                                        </span>
                                        <span v-else>
                                            {{partida_he.material.descripcion}}
                                        </span>
                                    </td>
                                    <td >
                                        {{partida_he.material.unidad}}
                                    </td>
                                    <td >
                                        <input type="text"
                                               v-on:keyup="calcularHE"
                                               class="form-control"
                                               :name="`cantidad_material_he[${i}]`"
                                               :data-vv-as="`Cantidad Material ${i+1}`"
                                               v-model="partida_he.cantidad"
                                               v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d+)?$/}"
                                               :class="{'is-invalid': errors.has(`cantidad_material_he[${i}]`)}"
                                               :id="`cantidad_material_he[${i}]`"
                                               style="text-align: right"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has(`cantidad_material_he[${i}]`)">{{ errors.first(`cantidad_material_he[${i}]`) }}</div>
                                    </td>
                                    <td >
                                        <input type="text"
                                               v-on:keyup="calcularHE"
                                               class="form-control"
                                               :name="`precio_unitario_he[${i}]`"
                                               :data-vv-as="`Precio Unitario ${i+1}`"
                                               v-model="partida_he.precio_unitario"
                                               v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d+)?$/}"
                                               :class="{'is-invalid': errors.has(`precio_unitario_he[${i}]`)}"
                                               :id="`precio_unitario_he[${i}]`"
                                               style="text-align: right"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has(`precio_unitario_he[${i}]`)">{{ errors.first(`precio_unitario_he[${i}]`) }}</div>

                                    </td>
                                    <td style="text-align: right">
                                        ${{partida_he.importe.formatMoney(2)}}
                                    </td>
                                    <td >
                                        <button  type="button" class="btn btn-outline-danger btn-sm" @click="eliminaPartidaHE(i)"  ><i class="fa fa-trash"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" style="text-align: right; border: none">Suma de Partidas de Herramienta y Equipo:</td>
                                    <td style="text-align: right; border: none">${{suma_partidas_he.formatMoney(2)}}</td>
                                    <td style="border: none"></td>
                                </tr>
                                <tr>
                                    <td style="border: none">&nbsp;</td>
                                </tr>

                                <!-- MAQUINARIA -->
                                <tr>
                                    <td colspan="5" style="border:none"><h6><i class="fa fa-truck"/>Maquinaria</h6></td>
                                    <td colspan="2" style="border:none"></td>
                                </tr>

                                <tr >
                                    <th class="encabezado icono">
                                        #
                                    </th>
                                    <th class="encabezado">
                                        Descripción
                                    </th>
                                    <th class="encabezado cantidad_input">
                                        Unidad
                                    </th>
                                    <th class="encabezado cantidad_input">
                                        Cantidad
                                    </th>
                                    <th class="encabezado cantidad_input">
                                        Precio Unitario
                                    </th>
                                    <th class="encabezado cantidad_input">
                                        Importe
                                    </th>
                                    <th class="encabezado icono">
                                        <button type="button" class="btn btn-success btn-sm"   :title="cargando?'Cargando...':'Agregar Partidas'" :disabled="cargando" @click="addPartidaMAQ()">
                                            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                            <i class="fa fa-plus" v-else></i>
                                        </button>
                                    </th>
                                </tr>
                                <tr v-for="(partida_maq, i) in partidas_maq">
                                    <td style="text-align: center">
                                        {{i+1}}
                                    </td>
                                    <td >
                                        <span v-if="partida_maq.material === ''">
                                            <MaterialSelect
                                                :name="`maquinaria[${i}]`"
                                                :scope="['maquinaria']"
                                                sort = "descripcion"
                                                v-model="partida_maq.material"
                                                data-vv-as="Material"
                                                v-validate="{required: true}"
                                                :placeholder="!cargando?'Seleccionar o buscar insumo por descripcion':'Cargando...'"
                                                :class="{'is-invalid': errors.has(`maquinaria[${i}]`)}"
                                                ref="MAQSelect"
                                                :disableBranchNodes="false"/>
                                            <div class="invalid-feedback" v-show="errors.has(`maquinaria[${i}]`)">{{ errors.first(`maquinaria[${i}]`) }}</div>
                                        </span>
                                        <span v-else>
                                            {{partida_maq.material.descripcion}}
                                        </span>
                                    </td>
                                    <td >
                                        {{partida_maq.material.unidad}}
                                    </td>
                                    <td >
                                        <input type="text"
                                               v-on:keyup="calcularMAQ"
                                               class="form-control"
                                               :name="`cantidad_material_maq[${i}]`"
                                               :data-vv-as="`Cantidad Material ${i+1}`"
                                               v-model="partida_maq.cantidad"
                                               v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d+)?$/}"
                                               :class="{'is-invalid': errors.has(`cantidad_material_maq[${i}]`)}"
                                               :id="`cantidad_material_maq[${i}]`"
                                               style="text-align: right"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has(`cantidad_material_maq[${i}]`)">{{ errors.first(`cantidad_material_maq[${i}]`) }}</div>
                                    </td>
                                    <td >
                                        <input type="text"
                                               v-on:keyup="calcularMAQ"
                                               class="form-control"
                                               :name="`precio_unitario_maq[${i}]`"
                                               :data-vv-as="`Precio Unitario ${i+1}`"
                                               v-model="partida_maq.precio_unitario"
                                               v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d+)?$/}"
                                               :class="{'is-invalid': errors.has(`precio_unitario_maq[${i}]`)}"
                                               :id="`precio_unitario_maq[${i}]`"
                                               style="text-align: right"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has(`precio_unitario_maq[${i}]`)">{{ errors.first(`precio_unitario_maq[${i}]`) }}</div>

                                    </td>
                                    <td style="text-align: right">
                                        ${{partida_maq.importe.formatMoney(2)}}
                                    </td>
                                    <td >
                                        <button  type="button" class="btn btn-outline-danger btn-sm" @click="eliminaPartidaMAQ(i)"  ><i class="fa fa-trash"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" style="text-align: right; border: none">Suma de Partidas de Maquiaria:</td>
                                    <td style="text-align: right; border: none">${{suma_partidas_maq.formatMoney(2)}}</td>
                                    <td style="border: none"></td>
                                </tr>
                                <tr>
                                    <td style="border: none">&nbsp;</td>
                                </tr>

                                <!-- SUBCONTRATOS -->
                                <tr>
                                    <td colspan="5" style="border:none"><h6><i class="fa fa-building"/>Subcontratos</h6></td>
                                    <td colspan="2" style="border:none"></td>
                                </tr>

                                <tr >
                                    <th class="encabezado icono">
                                        #
                                    </th>
                                    <th class="encabezado">
                                        Descripción
                                    </th>
                                    <th class="encabezado cantidad_input">
                                        Unidad
                                    </th>
                                    <th class="encabezado cantidad_input">
                                        Cantidad
                                    </th>
                                    <th class="encabezado cantidad_input">
                                        Precio Unitario
                                    </th>
                                    <th class="encabezado cantidad_input">
                                        Importe
                                    </th>
                                    <th class="encabezado icono">
                                        <button type="button" class="btn btn-success btn-sm"   :title="cargando?'Cargando...':'Agregar Partidas'" :disabled="cargando" @click="addPartidaSUB()">
                                            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                            <i class="fa fa-plus" v-else></i>
                                        </button>
                                    </th>
                                </tr>
                                <tr v-for="(partida_sub, i) in partidas_sub">
                                    <td style="text-align: center">
                                        {{i+1}}
                                    </td>
                                    <td >
                                        <span v-if="partida_sub.material === ''">
                                            <MaterialSelect
                                                :name="`subcontrato[${i}]`"
                                                :scope="['subcontrato']"
                                                sort = "descripcion"
                                                v-model="partida_sub.material"
                                                data-vv-as="Subcontrato"
                                                v-validate="{required: true}"
                                                :placeholder="!cargando?'Seleccionar o buscar insumo por descripcion':'Cargando...'"
                                                :class="{'is-invalid': errors.has(`subcontrato[${i}]`)}"
                                                ref="MAQSelect"
                                                :disableBranchNodes="false"/>
                                            <div class="invalid-feedback" v-show="errors.has(`subcontrato[${i}]`)">{{ errors.first(`subcontrato[${i}]`) }}</div>
                                        </span>
                                        <span v-else>
                                            {{partida_sub.material.descripcion}}
                                        </span>
                                    </td>
                                    <td >
                                        {{partida_sub.material.unidad}}
                                    </td>
                                    <td >
                                        <input type="text"
                                               v-on:keyup="calcularSUB"
                                               class="form-control"
                                               :name="`cantidad_material_sub[${i}]`"
                                               :data-vv-as="`Cantidad Material ${i+1}`"
                                               v-model="partida_sub.cantidad"
                                               v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d+)?$/}"
                                               :class="{'is-invalid': errors.has(`cantidad_material_sub[${i}]`)}"
                                               :id="`cantidad_material_sub[${i}]`"
                                               style="text-align: right"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has(`cantidad_material_sub[${i}]`)">{{ errors.first(`cantidad_material_sub[${i}]`) }}</div>
                                    </td>
                                    <td >
                                        <input type="text"
                                               v-on:keyup="calcularSUB"
                                               class="form-control"
                                               :name="`precio_unitario_sub[${i}]`"
                                               :data-vv-as="`Precio Unitario ${i+1}`"
                                               v-model="partida_sub.precio_unitario"
                                               v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d+)?$/}"
                                               :class="{'is-invalid': errors.has(`precio_unitario_sub[${i}]`)}"
                                               :id="`precio_unitario_sub[${i}]`"
                                               style="text-align: right"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has(`precio_unitario_sub[${i}]`)">{{ errors.first(`precio_unitario_sub[${i}]`) }}</div>

                                    </td>
                                    <td style="text-align: right">
                                        ${{partida_sub.importe.formatMoney(2)}}
                                    </td>
                                    <td >
                                        <button  type="button" class="btn btn-outline-danger btn-sm" @click="eliminaPartidaSUB(i)"  ><i class="fa fa-trash"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" style="text-align: right; border: none">Suma de Partidas de Subcontratos:</td>
                                    <td style="text-align: right; border: none">${{suma_partidas_sub.formatMoney(2)}}</td>
                                    <td style="border: none"></td>
                                </tr>
                            </template>
                            <template v-if="tipo_costo==2">
                                <!-- GASTOS -->
                                <tr>
                                    <td colspan="5" style="border:none"><h6><i class="fa fa-boxes"/>Gastos</h6></td>
                                    <td colspan="2" style="border:none"></td>
                                </tr>

                                <tr >
                                    <th class="encabezado icono">
                                        #
                                    </th>
                                    <th class="encabezado">
                                        Descripción
                                    </th>
                                    <th class="encabezado cantidad_input">
                                        Unidad
                                    </th>
                                    <th class="encabezado cantidad_input">
                                        Cantidad
                                    </th>
                                    <th class="encabezado cantidad_input">
                                        Precio Unitario
                                    </th>
                                    <th class="encabezado cantidad_input">
                                        Importe
                                    </th>
                                    <th class="encabezado icono">
                                        <button type="button" class="btn btn-success btn-sm"   :title="cargando?'Cargando...':'Agregar Partidas'" :disabled="cargando" @click="addPartidaGAS()">
                                            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                            <i class="fa fa-plus" v-else></i>
                                        </button>
                                    </th>
                                </tr>
                                <tr v-for="(partida_gas, i) in partidas_gas">
                                    <td style="text-align: center">
                                        {{i+1}}
                                    </td>
                                    <td >
                                        <span v-if="partida_gas.material === ''">
                                            <MaterialSelect
                                                :name="`gastos[${i}]`"
                                                :id="`gastos[${i}]`"
                                                :scope="['subcontrato']"
                                                sort = "descripcion"
                                                v-model="partida_gas.material"
                                                data-vv-as="Gastos"
                                                :isError="errors.has(`gastos[${i}]`)"
                                                v-validate="{required: true}"
                                                :placeholder="!cargando?'Seleccionar o buscar insumo por descripcion':'Cargando...'"
                                                :class="{'is-invalid': errors.has(`gastos[${i}]`)}"
                                                ref="GASSelect"
                                                :disableBranchNodes="false"/>
                                            <div class="invalid-feedback" v-show="errors.has(`gastos[${i}]`)">{{ errors.first(`gastos[${i}]`) }}</div>
                                        </span>
                                        <span v-else>
                                            {{partida_gas.material.descripcion}}
                                        </span>
                                    </td>
                                    <td >
                                        {{partida_gas.material.unidad}}
                                    </td>
                                    <td style="text-align: right">
                                        {{partida_gas.cantidad}}
                                    </td>
                                    <td >
                                        <input type="text"
                                           v-on:keyup="calcularGAS"
                                           class="form-control"
                                           :name="`precio_unitario_gas[${i}]`"
                                           :data-vv-as="`Precio Unitario ${i+1}`"
                                           v-model="partida_gas.precio_unitario"
                                           v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d+)?$/}"
                                           :class="{'is-invalid': errors.has(`precio_unitario_gas[${i}]`)}"
                                           :id="`precio_unitario_gas[${i}]`"
                                           style="text-align: right"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has(`precio_unitario_gas[${i}]`)">{{ errors.first(`precio_unitario_gas[${i}]`) }}</div>

                                    </td>
                                    <td style="text-align: right">
                                        ${{partida_gas.importe.formatMoney(2)}}
                                    </td>
                                    <td >
                                        <button  type="button" class="btn btn-outline-danger btn-sm" @click="eliminaPartidaGAS(i)"  ><i class="fa fa-trash"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" style="text-align: right; border: none">Suma de Partidas de Gastos:</td>
                                    <td style="text-align: right; border: none">${{suma_partidas_gas.formatMoney(2)}}</td>
                                    <td style="border: none"></td>
                                </tr>
                            </template>
                        </table>
                    </div>
                </div>

                <div class="row" >
                    <div class="col-md-12">
                        <hr style="border-color: #009a43 ">
                    </div>
                </div>

                <div class="row" >
                    <div class="col-md-12">
                        <label>
                            4.-Indique si la ruta de presupuesto donde se agregará el extraordinario ya existe en el presupuesto o generará una nueva:
                        </label>
                    </div>
                </div>

                <br>
                <div class="row">
                    <div class="col-md-12">

                        <div class="btn-group btn-group-toggle">
                            <label class="btn btn-outline-secondary" :class="tipo_ruta === Number(llave) ? 'active': ''" v-for="(tipo, llave) in tipos_ruta" :key="llave">
                                <i :class="llave==1 ?'fa fa-project-diagram':'fa fa-plus'"></i>
                                <input type="radio"
                                       class="btn-group-toggle"
                                       name="id_tipo"
                                       :id="'tipo' + llave"
                                       :value="llave"
                                       autocomplete="on"
                                       v-model.number="tipo_ruta">
                                        {{ tipo}}
                            </label>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row" >
                    <div class="col-md-12">
                        <hr style="border-color: #009a43 ">
                    </div>
                </div>
                <span v-if="tipo_ruta == 1">
                    <div class="row" >
                        <div class="col-md-12">
                            <label>
                                5.-Seleccione el concepto donde se agregará el nuevo concepto extraordinario:
                            </label>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <concepto-select
                                name="nodo_extraordinario"
                                data-vv-as="Concepto"
                                v-validate="{required: true}"
                                id="nodo_extraordinario"
                                v-model="id_nodo_extraordinario"
                                :error="errors.has('nodo_extraordinario')"
                                ref="conceptoSelect"
                                :disableBranchNodes="false"
                                :placeholder="'Seleccione el concepto donde se agregará el nuevo concepto extraordinario'"
                            ></concepto-select>
                        </div>
                    </div>
                    <br>
                </span>
                <span v-if="tipo_ruta == 2">
                    <div class="row" >
                        <div class="col-md-12">
                            <label>
                                5.-Seleccione el concepto que será el nodo para la nueva ruta a generar:
                            </label>
                        </div>
                    </div>
                    <br>
                    <div class="row" >
                        <div class="col-md-12">
                            <concepto-select
                                name="nodo_ruta_nueva"
                                data-vv-as="Concepto"
                                v-validate="{required: true}"
                                id="nodo_ruta_nueva"
                                v-model="id_nodo_ruta_nueva"
                                :error="errors.has('nodo_ruta_nueva')"
                                ref="conceptoSelect"
                                :disableBranchNodes="false"
                                :placeholder="'Seleccione el concepto que será el nodo para la nueva ruta a generar'"
                            ></concepto-select>
                        </div>
                    </div>
                    <br>
                    <div class="row" v-if="id_nodo_ruta_nueva>0">
                        <div class="col-md-12">
                            <hr style="border-color: #009a43 ">
                        </div>
                    </div>
                    <div class="row" v-if="id_nodo_ruta_nueva>0">
                        <div class="col-md-12">
                            <label>
                                 6.-Forme la nueva parte de la estructura del presupuesto a generar para el concepto extraordinario:
                            </label>
                        </div>
                    </div>
                    <div class="row" v-if="id_nodo_ruta_nueva>0" >
                        <div class="col-md-12">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th class="index_corto encabezado"></th>
                                        <th class="c120 encabezado">Clave</th>
                                        <th class="encabezado" >Descripción</th>
                                        <th class="icono encabezado">
                                            <button type="button" class="btn btn-success btn-sm" @click="agregarPartidaRuta('')">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                            <!--<button type="button" class="btn btn-success btn-sm"   :title="cargando?'Cargando...':'Agregar Partidas'" :disabled="cargando" @click="addPartidaGAS()">
                                            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                            <i class="fa fa-plus" v-else></i>
                                        </button>-->
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(partida, i) in partidas_nueva_ruta">
                                        <td class="icono">
                                            <button @click="agregarPartidaRuta(i)" type="button" class="btn btn-sm btn-outline-success" :disabled="cargando" title="Agregar">
                                                <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                                <i class="fa fa-plus" v-else></i>
                                            </button>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control"
                                                   :name="`clave[${i}]`"
                                                   data-vv-as="Clave"
                                                   v-model="partida.clave"
                                                   v-validate="{max:140}"
                                                   :class="{'is-invalid': errors.has(`clave[${i}]`)}"
                                                   :id="`clave[${i}]`">
                                            <div class="invalid-feedback" v-show="errors.has(`clave[${i}]`)">{{ errors.first(`clave[${i}]`) }}</div>
                                        </td>
                                        <td>
                                             <input type="text" class="form-control"
                                                    v-model="partida.descripcion"
                                                    readonly="readonly"
                                                    @click="habilitar(i, $event)"
                                                    @focusout="deshabilitar(i, $event)"
                                                    :name="`descripcion[${i}]`"
                                                    data-vv-as="Descripción"
                                                    v-validate="{required: partida.descripcion ===''}"
                                                    :class="{'is-invalid': errors.has(`descripcion[${i}]`) || partida.error ==1 || partida.descripcion_sin_formato.length > 255}"
                                                    :id="`descripcion_${i}`">
                                            <div class="invalid-feedback" v-show="errors.has(`descripcion[${i}]`)">{{ errors.first(`descripcion[${i}]`) }}</div>
                                            <div class="error-label" v-show="partida.descripcion_sin_formato.length > 255">La longitud del campo Descripción no debe ser mayor a 255 caracteres.</div>
                                        </td>
                                        <td class="icono">
                                            <button @click="eliminarPartidaRuta(i)" type="button" class="btn btn-sm btn-outline-danger pull-left" :disabled="!partida.es_hoja && partida.cantidad_hijos > 0" title="Eliminar">
                                                <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                                <i class="fa fa-trash" v-else></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </span>

                <div class="row" >
                    <div class="col-md-12">
                        <hr style="border-color: #009a43 ">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group row error-content">
                            <label for="motivo" class="col-sm-2 col-form-label">Motivo: </label>
                            <textarea
                                name="motivo"
                                id="motivo"
                                class="form-control"
                                data-vv-as="Motivo"
                                v-model="motivo"
                                v-validate="{required: true}"
                                :class="{'is-invalid': errors.has('motivo')}"
                            ></textarea>
                            <div class="invalid-feedback" v-show="errors.has('motivo')">{{ errors.first('motivo') }}</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group row error-content">
                            <label for="area_solicitante" class="col-form-label">Área Solicitante: </label>
                            <input type="text"
                                   class="form-control"
                                   name="area_solicitante"
                                   data-vv-as="Area Solicitante"
                                   v-model="area_solicitante"
                                   v-validate="{required: true}"
                                   :class="{'is-invalid': errors.has('area_solicitante')}"
                                   id="area_solicitante">
                            <div class="invalid-feedback" v-show="errors.has('area_solicitante')">{{ errors.first('area_solicitante') }}</div>
                        </div>
                    </div>
                </div>

                </span>

            </div>
            <div class="card-footer">
                <button class="btn btn-primary float-right" type="button" @click="validate"  :disabled="errors.count() > 0">
                    <i class="fa fa-save"></i>
                    Guardar
                </button>
            </div>
        </div>

    </span>
</template>

<script>
import {ModelListSelect} from 'vue-search-select';
import CreateConceptoExtaordinario from "../../contratos/solicitud-cambio/partials/CreateConceptoExtaordinario";
import ExtraordinarioDirectoCreate from "./partials/CreateDirecto";
import MaterialSelect from "../../cadeco/material/SelectAutocomplete";
import ConceptoSelect from "../../cadeco/concepto/SelectPadresDeMedibles";
export default {
    name: "variacion-volumen-create",
    components: {
        ConceptoSelect,
        MaterialSelect, ExtraordinarioDirectoCreate, CreateConceptoExtaordinario, ModelListSelect},
    props: [],
    data() {
        return {
            descripcion :'',
            unidad : '',
            unidades : [],
            cantidad : '',
            cargando: false,
            motivo:'',
            area_solicitante:'',
            concepto:null,
            suma_importe_cambio : 0,
            tipo_costo : '',
            tipo_ruta : 1,
            tipos_costo: {
                2: "Costo Indirecto",
                1: "Costo Directo"
            },
            tipos_ruta: {
                2: "Nueva",
                1: "Existente"
            },
            nodo_ruta_nueva : '',
            id_nodo_ruta_nueva : '',
            ruta_nueva : [],
            nodo_extraordinario : '',
            id_nodo_extraordinario : '',
            suma_partidas_material : 0,
            suma_partidas_mo : 0,
            suma_partidas_he : 0,
            suma_partidas_maq : 0,
            suma_partidas_sub : 0,
            suma_partidas_gas : 0,
            partidas_material: [
                {
                    i : 0,
                    material : "",
                    unidad : "",
                    numero_parte : "",
                    descripcion : "",
                    cantidad : "",
                    importe : 0,
                    precio_unitario : '',
                }
            ],
            partidas_mo: [
                {
                    i : 0,
                    material : "",
                    unidad : "",
                    numero_parte : "",
                    descripcion : "",
                    cantidad : "",
                    importe : 0,
                    precio_unitario : '',
                }
            ],
            partidas_he: [
                {
                    i : 0,
                    material : "",
                    unidad : "",
                    numero_parte : "",
                    descripcion : "",
                    cantidad : "",
                    importe : 0,
                    precio_unitario : '',
                }
            ],
            partidas_maq: [
                {
                    i : 0,
                    material : "",
                    unidad : "",
                    numero_parte : "",
                    descripcion : "",
                    cantidad : "",
                    importe : 0,
                    precio_unitario : '',
                }
            ],
            partidas_sub: [
                {
                    i : 0,
                    material : "",
                    unidad : "",
                    numero_parte : "",
                    descripcion : "",
                    cantidad : "",
                    importe : 0,
                    precio_unitario : '',
                }
            ],
            partidas_gas: [
                {
                    i : 0,
                    material : "",
                    unidad : "",
                    numero_parte : "",
                    descripcion : "",
                    cantidad : 1,
                    importe : 0,
                    precio_unitario : '',
                }
            ],
            partidas_nueva_ruta : [],
        }
    },
    methods: {
        getUnidades() {
            this.cargando = true;
            return this.$store.dispatch('cadeco/unidad/index',{
                params: {sort: 'unidad',  order: 'asc'}
            })
                .then(data => {
                    this.unidades = data.data;
                }).finally(() => {
                    this.cargando = false;
                })
        },
        eliminaPartidaMaterial(i){
            this.partidas_material.splice(i, 1);
            this.calcular();
        },
        eliminaPartidaMO(i){
            this.partidas_mo.splice(i, 1);
            this.calcularMO();
        },
        eliminaPartidaHE(i){
            this.partidas_he.splice(i, 1);
            this.calcularHE();
        },
        eliminaPartidaMAQ(i){
            this.partidas_maq.splice(i, 1);
            this.calcularMAQ();
        },
        eliminaPartidaSUB(i){
            this.partidas_sub.splice(i, 1);
            this.calcularSUB();
        },
        eliminaPartidaGAS(i){
            this.partidas_gas.splice(i, 1);
            this.calcularGAS();
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    this.store()
                }
            });
        },
        store() {
            this.cargando = true;

            var datos_solicitud_cambio = {
                'motivo' : this.motivo,
                'area_solicitante' : this.area_solicitante,
                'concepto' : this.concepto
            }

            return this.$store.dispatch('control-presupuesto/variacion-volumen/store', datos_solicitud_cambio)
            .then(data => {
                this.$router.push({name: 'variacion-volumen'});
            })
            .finally(() => {
                this.cargando = false;
            })
        },
        calcular() {
            let _self = this;
            this.suma_partidas_material = 0;
            this.partidas_material.forEach(function (partida, i) {
                let cantidad = 0;
                let precio_unitario = 0;
                if(isNaN(parseFloat(partida.cantidad)))
                {
                    cantidad = 0;
                }else{
                    cantidad = partida.cantidad;
                }

                if(isNaN(parseFloat(partida.precio_unitario)))
                {
                    precio_unitario = 0;
                }else{
                    precio_unitario = partida.precio_unitario;
                }

                partida.importe = parseFloat(cantidad) * parseFloat(precio_unitario);
                _self.suma_partidas_material += parseFloat(partida.importe);
            });
        },
        calcularMO() {
            let _self = this;
            this.suma_partidas_mo = 0;
            this.partidas_mo.forEach(function (partida, i) {
                let cantidad = 0;
                let precio_unitario = 0;
                if(isNaN(parseFloat(partida.cantidad)))
                {
                    cantidad = 0;
                }else{
                    cantidad = partida.cantidad;
                }

                if(isNaN(parseFloat(partida.precio_unitario)))
                {
                    precio_unitario = 0;
                }else{
                    precio_unitario = partida.precio_unitario;
                }

                partida.importe = parseFloat(cantidad) * parseFloat(precio_unitario);
                _self.suma_partidas_mo += parseFloat(partida.importe);
            });
        },
        calcularHE() {
            let _self = this;
            this.suma_partidas_he = 0;
            this.partidas_he.forEach(function (partida, i) {
                let cantidad = 0;
                let precio_unitario = 0;

                if(isNaN(parseFloat(partida.cantidad)))
                {
                    cantidad = 0;
                }else{
                    cantidad = partida.cantidad;
                }

                if(isNaN(parseFloat(partida.precio_unitario)))
                {
                    precio_unitario = 0;
                }else{
                    precio_unitario = partida.precio_unitario;
                }

                partida.importe = parseFloat(cantidad) * parseFloat(precio_unitario);
                _self.suma_partidas_he += parseFloat(partida.importe);
            });
        },
        calcularMAQ() {
            let _self = this;
            this.suma_partidas_maq = 0;
            this.partidas_maq.forEach(function (partida, i) {
                let cantidad = 0;
                let precio_unitario = 0;

                if(isNaN(parseFloat(partida.cantidad)))
                {
                    cantidad = 0;
                }else{
                    cantidad = partida.cantidad;
                }

                if(isNaN(parseFloat(partida.precio_unitario)))
                {
                    precio_unitario = 0;
                }else{
                    precio_unitario = partida.precio_unitario;
                }

                partida.importe = parseFloat(cantidad) * parseFloat(precio_unitario);
                _self.suma_partidas_maq += parseFloat(partida.importe);
            });
        },
        calcularSUB() {
            let _self = this;
            this.suma_partidas_sub = 0;
            this.partidas_sub.forEach(function (partida, i) {
                let cantidad = 0;
                let precio_unitario = 0;

                if(isNaN(parseFloat(partida.cantidad)))
                {
                    cantidad = 0;
                }else{
                    cantidad = partida.cantidad;
                }

                if(isNaN(parseFloat(partida.precio_unitario)))
                {
                    precio_unitario = 0;
                }else{
                    precio_unitario = partida.precio_unitario;
                }

                partida.importe = parseFloat(cantidad) * parseFloat(precio_unitario);
                _self.suma_partidas_sub += parseFloat(partida.importe);
            });
        },
        calcularGAS() {
            let _self = this;
            this.suma_partidas_gas = 0;
            this.partidas_gas.forEach(function (partida, i) {
                let cantidad = 0;
                let precio_unitario = 0;

                if(isNaN(parseFloat(partida.cantidad)))
                {
                    cantidad = 0;
                }else{
                    cantidad = partida.cantidad;
                }

                if(isNaN(parseFloat(partida.precio_unitario)))
                {
                    precio_unitario = 0;
                }else{
                    precio_unitario = partida.precio_unitario;
                }

                partida.importe = parseFloat(cantidad) * parseFloat(precio_unitario);
                _self.suma_partidas_gas += parseFloat(partida.importe);
            });
        },
        addPartidaMaterial(){
            this.partidas_material.splice(this.partidas_material.length + 1, 0, {
                i : 0,
                material : "",
                unidad : "",
                numero_parte : "",
                descripcion : "",
                cantidad : "",
                importe : 0,
                precio_unitario : 0,
            });
            this.index = this.index+1;
        },
        addPartidaMO(){
            this.partidas_mo.splice(this.partidas_mo.length + 1, 0, {
                i : 0,
                material : "",
                unidad : "",
                numero_parte : "",
                descripcion : "",
                cantidad : "",
                importe : 0,
                precio_unitario : 0,
            });
            this.index = this.index+1;
        },
        addPartidaHE(){
            this.partidas_he.splice(this.partidas_he.length + 1, 0, {
                i : 0,
                material : "",
                unidad : "",
                numero_parte : "",
                descripcion : "",
                cantidad : "",
                importe : 0,
                precio_unitario : 0,
            });
            this.index = this.index+1;
        },
        addPartidaMAQ(){
            this.partidas_maq.splice(this.partidas_maq.length + 1, 0, {
                i : 0,
                material : "",
                unidad : "",
                numero_parte : "",
                descripcion : "",
                cantidad : "",
                importe : 0,
                precio_unitario : 0,
            });
            this.index = this.index+1;
        },
        addPartidaSUB(){
            this.partidas_sub.splice(this.partidas_sub.length + 1, 0, {
                i : 0,
                material : "",
                unidad : "",
                numero_parte : "",
                descripcion : "",
                cantidad : "",
                importe : 0,
                precio_unitario : 0,
            });
            this.index = this.index+1;
        },
        addPartidaGAS(){
            this.partidas_gas.splice(this.partidas_gas.length + 1, 0, {
                i : 0,
                material : "",
                unidad : "",
                numero_parte : "",
                descripcion : "",
                cantidad : 1,
                importe : 0,
                precio_unitario : 0,
            });
            this.index = this.index+1;
        },
        agregarPartidaRuta(index){
            if(index === ''){
                this.partidas_nueva_ruta.push({
                    clave:'',
                    descripcion:'',
                    descripcion_sin_formato:'',
                    nivel: 1,
                    es_hoja:true,
                    cantidad_hijos:0,
                });
            }else{
                let temp_index = index + 1;
                while(temp_index in this.partidas_nueva_ruta && this.partidas_nueva_ruta[temp_index].nivel >= +this.partidas_nueva_ruta[index].nivel + 1){
                    temp_index= temp_index + 1;
                }
                this.partidas_nueva_ruta.splice(temp_index, 0, {
                    clave:'',
                    descripcion:'',
                    descripcion_sin_formato:'',
                    nivel:this.partidas_nueva_ruta[index].nivel + 1,
                    es_hoja:true,
                    cantidad_hijos:0,
                });

                this.partidas_nueva_ruta[index].es_hoja = false;
                this.partidas_nueva_ruta[index].es_rama = true;
                this.partidas_nueva_ruta[index].cantidad_hijos = this.partidas_nueva_ruta[index].cantidad_hijos + 1;
            }

        },
        eliminarPartidaRuta(index){
            if(this.partidas_nueva_ruta[index].nivel === 1){
                this.partidas_nueva_ruta.splice(index, 1);
            }else{
                let temp_index = index - 1;
                while(temp_index in this.partidas_nueva_ruta && this.partidas_nueva_ruta[temp_index].nivel == +this.partidas_nueva_ruta[index].nivel){
                    temp_index= temp_index - 1;
                }
                this.partidas_nueva_ruta[temp_index].cantidad_hijos = this.partidas_nueva_ruta[temp_index].cantidad_hijos - 1;
                this.partidas_nueva_ruta.splice(index, 1);
                if(this.partidas_nueva_ruta[temp_index].cantidad_hijos == 0){
                    this.partidas_nueva_ruta[temp_index].es_hoja = true;
                }
            }
        },
        habilitar : function(i, event){
            let nuevo_valor = this.descripcionSinFormat(i);
            this.partidas_nueva_ruta[i].descripcion = nuevo_valor;
            this.partidas_nueva_ruta[i].descripcion_sin_formato = nuevo_valor;
            $("#" + event.target.id).removeAttr("readonly");
        },
        deshabilitar : function(i,event){
            let isReadOnly = $("#" + event.target.id).attr("readonly");
            if(isReadOnly !== "readonly"){
                this.partidas_nueva_ruta[i].descripcion_sin_formato = this.descripcionSinFormat(i);
                let nuevo_valor = this.descripcionFormat(i);
                this.partidas_nueva_ruta[i].descripcion = nuevo_valor;
                $("#" + event.target.id).attr("readonly",true);
            }
        },
        descripcionFormat(i){
            var len = this.partidas_nueva_ruta[i].descripcion.length + (+this.partidas_nueva_ruta[i].nivel * 3);
            return this.partidas_nueva_ruta[i].descripcion.padStart(len, "_")
        },
        descripcionSinFormat(i){
            var len = (this.partidas_nueva_ruta[i].nivel * 3);
            let lineas = '';
            lineas = lineas.padStart(len, "_");
            return this.partidas_nueva_ruta[i].descripcion.replace(lineas, '');
        },
    },
    computed: {
        precio_unitario() {
            return this.suma_partidas_material +
                this.suma_partidas_mo +
                this.suma_partidas_he +
                this.suma_partidas_maq +
                this.suma_partidas_sub +
                this.suma_partidas_gas;
        },
        monto_presupuestado() {
            return this.precio_unitario * this.cantidad;
        },
    },
    mounted() {
        this.getUnidades();
    },
    watch:{
        /*id_nodo_ruta_nueva(value){
            if(value != ''){
                return this.$store.dispatch('cadeco/concepto/find', {
                    id: value,
                    params: {
                    }
                })
                .then(data => {
                    this.nodo_ruta_nueva = data;
                })
            }
        },
        id_nodo_extraordinario(value){
            let _self = this;
            if(value != ''){
                return this.$store.dispatch('cadeco/concepto/find', {
                    id: value,
                    params: {
                    }
                })
                .then(data => {
                    _self.nodo_extraordinario = data;
                }).finally(() => {
                        console.log("fin");
                    })
            }
        },*/
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
