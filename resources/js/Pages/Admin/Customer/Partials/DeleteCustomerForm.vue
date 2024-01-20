<template>
  <div>
    <Modal :show="isDeleteCustomer" @close="closeModal" maxWidth="xl">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900">
          Xóa dữ liệu khách hàng
        </h2>

        <div class="p-4 md:p-5">
          <form class="max-w-sm mx-auto" action="#" @submit.prevent="submit">
            <div class="mb-5">
              <label for="roles" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Lệnh</label>
              <select id="roles" v-model="form.command" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option value="">Chọn lệnh</option>
                <option v-for="(vi, command) in commands" :key="command" :value="command">{{ vi }}</option>
              </select>
              <InputError :message="form.errors.command" class="mt-2" />
            </div>
            <!-- <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Tạo tài khoản</button> -->
            <div class="mt-6 flex justify-end">
              <SecondaryButton @click="closeModal"> Hủy </SecondaryButton>
    
              <DangerButton
                class="ms-3"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                @click="del"
              >
                Xóa
              </DangerButton>
            </div>
          </form>
        </div>
      </div>
    </Modal>
  </div>
</template>
<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Admin/Modal.vue';
import SecondaryButton from '@/Components/Admin/SecondaryButton.vue';
import { router, useForm } from '@inertiajs/vue3';
import { reactive } from 'vue';
import InputError from '@/Components/Admin/InputError.vue';

const props = defineProps({
  isDeleteCustomer: Boolean,
});

const commands = reactive({
  all: 'Tất cả',
  duplicate: 'Trùng',
  sales_state: 'Trạng thái',
});

const form = useForm({
  command: '',
});

const del = () => {
  form.delete(route('admin.customers.destroy'), {
    preserveScroll: true,
    onSuccess: () => closeModal(),
  });
};
const emit = defineEmits(['closeForm']);

const closeModal = () => {
  emit('closeForm', 'isDeleteCustomer');

  form.reset();
};
</script>
