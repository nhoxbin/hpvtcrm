<template>
  <div>
    <Modal :show="isEditCustomer" @close="closeModal" max-width="xl">
      <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
        <!-- Modal header -->
        <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
          <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
            Sửa trạng thái
          </h3>
          <button type="button" @click="closeModal" class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
            </svg>
            <span class="sr-only">Close modal</span>
          </button>
        </div>
        <!-- Modal body -->
        <div class="p-4 md:p-5">
          <form class="max-w-sm mx-auto" action="#" @submit.prevent="submit">
            <div class="mb-5">
              <label for="roles" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Trạng thái</label>
              <select id="roles" v-model="form.sales_state" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option value="">Chọn trạng thái</option>
                <option v-for="(state, name) in sales_states" :key="name" :value="name">{{ state }}</option>
              </select>
              <InputError :message="form.errors.sales_state" class="mt-2" />
            </div>
            <div class="mb-5">
              <label for="sales_note" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ghi chú</label>
              <textarea type="text" id="sales_note" v-model="form.sales_note" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
              </textarea>
              <InputError :message="form.errors.sales_note" class="mt-2" />
            </div>
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Cập nhật</button>
          </form>
        </div>
      </div>
    </Modal>
  </div>
</template>
<script setup>
import InputError from '@/Components/Admin/InputError.vue';
import Modal from '@/Components/Admin/Modal.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  customer: {
    required: true,
    type: Object,
  },
  sales_states: Object,
  isEditCustomer: Boolean,
});

const form = useForm({
  sales_state: props.customer.sales_state || '',
  sales_note: props.customer.sales_note,
});

const emit = defineEmits(['closeForm']);

const closeModal = () => {
  emit('closeForm', 'isEditCustomer');

  form.reset();
};

const submit = () => {
  form.put(route('customers.update', props.customer.id), {
    preserveScroll: true,
    preserveState: false,
    onSuccess: () => closeModal(),
  });
};
</script>
