<template>
    <span>
     <div id="myCarousel" class="carousel slide carousel-fade" data-ride="carousel">
         <!--<ul class="carousel-indicators">
             <li class="active"   data-target="#item" data-slide-to="0" ></li>
             <li data-slide-to="1" data-target="#item" ></li>
             <li data-slide-to="2" data-target="#item" ></li>
         </ul>-->
         <div class="carousel-inner">
             <div class="carousel-item active">
                 <img src="../../../../../public/uploads/padron_contratistas/MUC1803013I7/imagen.png" alt="Los dfsdfsdfdfsdfs" width="800" height="500">
                 <div class="carousel-caption">
                     <h3>New Yodsfdfsrk</h3>
                     <p>We love the dfsdfsdf Apple!</p>
                 </div>
             </div>
             <div class="carousel-item">
                 <img src="../../../../../public/uploads/padron_contratistas/MUC1803013I7/imagen_prueba.png" alt="Los Angedsfsdfsdfles" width="800" height="500">
                 <div class="carousel-caption">
                     <h3>prueba 2</h3>
                     <p>We love the Big Apple!</p>
                 </div>
             </div>
             <div class="carousel-item">
                 <img src="../../../../../public/uploads/padron_contratistas/MUC1803013I7/imagenprueba.jpg" alt="Los Artert534534ngeles" width="800" height="500">
                 <div class="carousel-caption">
                     <h3>prueba 33333</h3>
                     <p>We love the Big Apple!</p>
                 </div>
             </div>
         </div>
         <a class="carousel-control-prev" href="#myCarousel" data-slide="prev">
             <span class="carousel-control-prev-icon"></span>
         </a>
         <a class="carousel-control-next" href="#myCarousel" data-slide="next">
             <span class="carousel-control-next-icon"></span>
         </a>
     </div>
    </span>
</template>

<script>
    export default {
        name: "Imagen",
        components: {

        },
        data() {
            return {
                url : '/public/uploads/padron_contratistas/MUC1803013I7',
                url1 : '../../../../../public/uploads/padron_contratistas/MUC1803013I7/',
                imagenes: {
                    3: "imagen_prueba.png",
                    2: "imagenprueba.png",
                    1: "imagen.png"
                },
            };
        },
    }
</script>
<style scoped>
    .carousel-inner img {
        width: 100%;
        height: 100%;
    }
</style>
