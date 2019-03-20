<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-header">CONFIGURACIÓN DE OBRA</li>
            <li class="nav-item">
                <a href="#identificacion" class="nav-link">
                    <i class="fa fa-circle-o nav-icon"></i>
                    <p>Identificación</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="#informacion_financiera" class="nav-link">
                    <i class="fa fa-circle-o nav-icon"></i>
                    <p>Información Financiera</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="#plan" class="nav-link">
                    <i class="fa fa-circle-o nav-icon"></i>
                    <p>Plan de Obra</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="#seguridad" class="nav-link">
                    <i class="fa fa-circle-o nav-icon"></i>
                    <p>Seguridad</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="#ubicacion" class="nav-link">
                    <i class="fa fa-circle-o nav-icon"></i>
                    <p>Ubicación</p>
                </a>
            </li>

            <li class="nav-header">CONFIGURACIÓN CONTABLE</li>
            <li class="nav-item">
                <a href="#contpaq" class="nav-link">
                    <i class="fa fa-circle-o nav-icon"></i>
                    <p>COntPaq</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="#pagos" class="nav-link">
                    <i class="fa fa-circle-o nav-icon"></i>
                    <p>Pagos</p>
                </a>
            </li>
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</template>

<script>
    export default {
        name: "configuracion-menu",

        methods: {
            mostrarMenu(event) {
                event.stopPropagation();
                $(event.target).closest('li').toggleClass('menu-open');
            }
        },

        computed: {
            catalogos() {
                return this.$root.can([
                    'consultar_cuenta_almacen',
                    'consultar_cuenta_contable_bancaria',
                    'consultar_cuenta_concepto',
                    'consultar_cuenta_costo',
                    'consultar_cuenta_empresa',
                    'consultar_cuenta_fondo',
                    'consultar_cuenta_general',
                    'consultar_cuenta_material'
                ])
            },

            modulos() {
                return this.$root.can([
                    'consultar_cierre_periodo',
                    'consultar_prepolizas_generadas'
                ])
            }
        }
    }
</script>

<style scoped>
    .sidebar-form, .nav-sidebar > .nav-header {
        padding: 1rem 0.5rem 0.5rem 1rem;
    }
</style>