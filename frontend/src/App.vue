<script setup>
import { ref } from 'vue';
import ShopHomePage from './components/pages/ShopHomePage/ShopHomePage.vue';
import ProductDetailPage from './components/pages/ProductDetailPage/ProductDetailPage.vue';

const showDetail = ref(false);
const selectedId = ref(null);

function openDetail(id) {
  selectedId.value = id;
  showDetail.value = true;
}

function goHome() {
  showDetail.value = false;
}
</script>

<template>
  <component
    :is="showDetail ? ProductDetailPage : ShopHomePage"
    :id="selectedId"
    @view-product="openDetail"
    @back="goHome"
  />
</template>
