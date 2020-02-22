<template>
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item"  v-if="$root.can('editar_poliza',true)">
                <router-link :to="{name: 'editar-poliza'}" class="nav-link" :class="{active: this.$route.name == 'editar-poliza'}">
                    <i class="fa fa-edit nav-icon"></i>
                    <p>Editar Póliza</p>
                </router-link>
            </li>

            <li class="nav-item">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <i class="fa fa-circle nav-icon"></i>
                    <p>
                       Configuración
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item" v-if="$root.can('editar-poliza') || true">
                        <router-link :to="{name: 'lista-empresa'}" class="nav-link" :class="{active: this.$route.name == 'lista-empresa'}">
                            &nbsp;<i class="fa fa-circle-o nav-icon"></i>
                            <p>Empresas</p>
                        </router-link>
                    </li>
                </ul>
            </li>

            
        </ul>
    </nav>
</template>

<script>
    export default {
        name: "contabilidad-general-menu",
        methods: {
            mostrarMenu(event) {
                event.stopPropagation();
                $(event.target).closest('li').toggleClass('menu-open');
            }
        }
    }
</script>

