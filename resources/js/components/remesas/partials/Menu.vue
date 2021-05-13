<template>
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item"  v-if="$root.can('consultar_limite_remesa',true)">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <i class="fa fa-cog"></i>
                    <p>Configuración</p>
                    <i class="right fa fa-angle-left"></i>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item"  v-if="$root.can('consultar_limite_remesa',true)">
                        <router-link :to="{name: 'limite-remesa'}" class="nav-link" :class="{active: this.$route.name == 'limite-remesa'}">
                            <i class="fa fa-file-alt nav-icon"></i>
                            <p>Limite Remesas</p>
                        </router-link>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</template>

<script>
    export default {
        name: "remesas-menu",
        methods: {
            mostrarMenu(event) {
                event.stopPropagation();
                $(event.target).closest('li').toggleClass('menu-open');
            }
        }
    }
</script>



<style scoped>

</style>
