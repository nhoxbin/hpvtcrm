<template>
  <div>
    <Modal :show="isDistributeCustomer" @close="closeModal">
      <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
        <!-- Modal header -->
        <div
          class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600"
        >
          <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
            Phân phối khách hàng đến sales
          </h3>
          <button
            type="button"
            class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
            @click="closeModal"
          >
            <svg
              class="w-3 h-3"
              aria-hidden="true"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 14 14"
            >
              <path
                stroke="currentColor"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"
              />
            </svg>
            <span class="sr-only">Close modal</span>
          </button>
        </div>
        <!-- Modal body -->
        <div class="p-4 md:p-5">
          <form class="max-w-sm mx-auto" @submit.prevent="submit">
            <select
              v-model="formSearch.user_id"
              multiple
              size="10"
              class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
            >
              <option value="all">Chia đều</option>
              <option v-for="user in users" :key="user.id" :value="user.id">
                {{ user.name }}
              </option>
            </select>
            <button
              type="submit"
              :disabled="formSearch.processing"
              class="mt-2 w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
            >
              Phân phối
            </button>
          </form>
        </div>
      </div>
    </Modal>
  </div>
</template>

<script setup>
import Modal from "@/Components/Admin/Modal.vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
  users: Array,
  formSearch: Object,
  isDistributeCustomer: Boolean,
});

const emit = defineEmits(["closeForm"]);

const closeModal = () => {
  emit("closeForm", "isDistributeCustomer");
  props.formSearch.reset();
  router.reload({ only: ["customers"] });
};

const submit = () => {
  props.formSearch.put(route("admin.onebss.customers.distribute"), {
    preserveScroll: true,
    onSuccess: () => closeModal(),
  });
};
</script>
