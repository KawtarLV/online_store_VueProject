<template>
  <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-6 flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-semibold text-slate-900">Add product</h1>
        <p class="text-sm text-slate-600">Create a new product and upload images.</p>
      </div>
      <a href="#/dashboard" class="text-sm font-semibold text-slate-700 hover:text-slate-900">
        Back to dashboard
      </a>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <form class="grid gap-6 md:grid-cols-2" @submit.prevent="handleSubmit">
        <div>
          <label class="block text-sm font-medium text-slate-700">Name</label>
          <input v-model="form.name" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2" required />
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700">Brand</label>
          <input v-model="form.brand" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2" />
        </div>

        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-slate-700">Description</label>
          <textarea v-model="form.description" rows="4" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2" required />
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700">Price</label>
          <input v-model.number="form.price" type="number" min="0" step="0.01" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2" required />
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700">Stock</label>
          <input v-model.number="form.stock" type="number" min="0" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2" required />
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700">Category</label>
          <select v-model.number="form.categoryId" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
            <option :value="null">Select category</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700">Rating</label>
          <input v-model.number="form.rating" type="number" min="0" max="5" step="0.1" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2" />
        </div>

        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-slate-700">Main image</label>
          <input type="file" accept="image/*" class="mt-1 w-full text-sm" @change="onMainFile" />
          <img v-if="mainPreview" :src="mainPreview" alt="Main preview" class="mt-3 h-40 w-40 rounded-xl object-cover" />
        </div>

        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-slate-700">Gallery images</label>
          <input type="file" accept="image/*" multiple class="mt-1 w-full text-sm" @change="onGalleryFiles" />
          <div class="mt-3 flex flex-wrap gap-3">
            <div v-for="(file, index) in galleryFiles" :key="index" class="rounded-xl border border-slate-200 p-2">
              <p class="max-w-40 text-xs text-slate-600">{{ file.name }}</p>
            </div>
          </div>
        </div>

        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-slate-700">Specs</label>
          <div class="mt-2 space-y-2">
            <div v-for="(spec, index) in specs" :key="index" class="grid grid-cols-[1fr_1fr_auto] gap-2">
              <input v-model="spec.key" placeholder="Key" class="rounded-lg border border-slate-300 px-3 py-2 text-sm" />
              <input v-model="spec.value" placeholder="Value" class="rounded-lg border border-slate-300 px-3 py-2 text-sm" />
              <button type="button" class="rounded-lg bg-slate-100 px-3 text-sm" @click="removeSpec(index)">✕</button>
            </div>
            <button type="button" class="rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white" @click="addSpec">
              Add spec
            </button>
          </div>
        </div>

        <div class="md:col-span-2 flex items-center gap-3">
          <button type="submit" class="rounded-lg bg-slate-900 px-5 py-2 text-sm font-semibold text-white hover:bg-slate-800" :disabled="saving">
            {{ saving ? 'Saving...' : 'Create product' }}
          </button>
          <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
          <p v-if="success" class="text-sm text-green-600">Product created.</p>
        </div>
      </form>
    </div>
  </section>
</template>

<script setup>
import { onMounted, onBeforeUnmount, reactive, ref } from 'vue';
import { get, post, uploadFile } from '../../../utils/api.js';

const props = defineProps({
  token: {
    type: String,
    default: '',
  },
});

const form = reactive({
  name: '',
  description: '',
  price: 0,
  stock: 0,
  brand: '',
  rating: 0,
  categoryId: null,
});

const categories = ref([]);
const specs = ref([]);
const mainFile = ref(null);
const galleryFiles = ref([]);
const mainPreview = ref('');
const saving = ref(false);
const success = ref(false);
const error = ref('');

function addSpec() {
  specs.value.push({ key: '', value: '' });
}

function removeSpec(index) {
  specs.value.splice(index, 1);
}

function onMainFile(event) {
  const file = event.target.files?.[0] || null;
  mainFile.value = file;

  if (mainPreview.value) {
    URL.revokeObjectURL(mainPreview.value);
  }

  mainPreview.value = file ? URL.createObjectURL(file) : '';
}

function onGalleryFiles(event) {
  galleryFiles.value = Array.from(event.target.files || []).slice(0, 2);
}

function buildSpecs() {
  const output = {};
  specs.value.forEach((spec) => {
    if (spec.key.trim() !== '') {
      output[spec.key.trim()] = spec.value;
    }
  });
  return Object.keys(output).length > 0 ? output : null;
}

async function loadCategories() {
  try {
    const response = await get('/categories');
    const data = await response.json();
    if (response.ok) {
      categories.value = Array.isArray(data) ? data : [];
    }
  } catch {
    categories.value = [];
  }
}

async function uploadImages() {
  let mainImage = '';
  const extraImages = [];

  if (mainFile.value) {
    const response = await uploadFile('/upload', mainFile.value);
    const data = await response.json();
    if (!response.ok) {
      throw new Error(data.error || 'Main image upload failed');
    }
    mainImage = data.url || '';
  }

  for (const file of galleryFiles.value) {
    const response = await uploadFile('/upload', file);
    const data = await response.json();
    if (!response.ok) {
      throw new Error(data.error || 'Gallery image upload failed');
    }
    if (data.url) {
      extraImages.push(data.url);
    }
  }

  return { mainImage, extraImages };
}

async function handleSubmit() {
  error.value = '';
  success.value = false;
  saving.value = true;

  try {
    const { mainImage, extraImages } = await uploadImages();
    const response = await post('/products', {
      name: form.name,
      description: form.description,
      price: form.price,
      stock: form.stock,
      brand: form.brand,
      rating: form.rating,
      categoryId: form.categoryId,
      specs: buildSpecs(),
      image: mainImage,
      images: extraImages,
    }, {
      headers: {
        Authorization: `Bearer ${props.token}`,
      },
    });
    const data = await response.json();

    if (!response.ok) {
      error.value = data.error || 'Create failed';
      return;
    }

    success.value = true;
    window.setTimeout(() => {
      window.location.hash = '#/dashboard';
    }, 700);
  } catch (err) {
    error.value = err.message || 'Network error while creating product';
  } finally {
    saving.value = false;
  }
}

onMounted(loadCategories);
onBeforeUnmount(() => {
  if (mainPreview.value) {
    URL.revokeObjectURL(mainPreview.value);
  }
});
</script>
