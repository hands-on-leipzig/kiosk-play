<script setup>
import '@splidejs/vue-splide/css';
</script>

<template>
  <Splide :options="{
    autoplay: true,
  rewind: true,
 interval: 5000,
type: 'fade',
arrows: false,
pauseOnHover: false,
pauseOnFocus: false,
                }" aria-label="My Favorite Images">
    <SplideSlide>
      Kuba
    </SplideSlide>
    <SplideSlide>
      Janni
    </SplideSlide>
  </Splide>
</template>

<style scoped>

</style>