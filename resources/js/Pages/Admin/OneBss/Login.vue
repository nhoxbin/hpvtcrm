<template>
  <Head title="Khách hàng"/>

  <AuthenticatedLayout>
    <template #header>
      Admin Login OneBss
    </template>

    <form @submit.prevent="form.post(route('admin.onebss.store'))" class="mt-6 space-y-6">
      <section v-if="step == 1">
        <div>
          <InputLabel for="username" value="Username" />

          <TextInput
            id="username"
            type="text"
            class="mt-1 block w-full"
            v-model="formLogin.username"
            required
            autofocus
            autocomplete="username"
          />

          <InputError class="mt-2" :message="formLogin.errors.username" />
        </div>

        <div>
          <InputLabel for="password" value="Password" />

          <TextInput
            id="password"
            type="password"
            class="mt-1 block w-full"
            v-model="formLogin.password"
            required
            autocomplete="password"
          />

          <InputError class="mt-2" :message="formLogin.errors.password" />
        </div>
      </section>
      <section v-if="step == 2">
        <div>
          <InputLabel for="otp" value="OTP" />

          <TextInput
            id="otp"
            type="text"
            class="mt-1 block w-full"
            v-model="formOAuth.otp"
            required
            autofocus
          />

          <InputError class="mt-2" :message="formOAuth.errors.otp" />
        </div>
      </section>

        <div class="flex items-center gap-4">
          <PrimaryButton v-if="step == 1" @click.prevent="getOTP">Lấy OTP</PrimaryButton>
          <PrimaryButton v-if="step == totalSteps" @click.prevent="login">Đăng nhập</PrimaryButton>

          <Transition
            enter-active-class="transition ease-in-out"
            enter-from-class="opacity-0"
            leave-active-class="transition ease-in-out"
            leave-to-class="opacity-0"
          >
            <!-- <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Thành công.</p> -->
            <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
              {{ status }}
            </div>
          </Transition>
        </div>
    </form>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
import 'element-plus/es/components/message/style/css';
import 'element-plus/es/components/message-box/style/css';
// import { ElMessage, ElMessageBox } from 'element-plus';
// import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButton from '@/Components/Admin/PrimaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
// import { Link } from '@inertiajs/vue3';

import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref } from 'vue';

const props = defineProps({
    status: String,
    error: Boolean,
    secretCode: String,
});

const formLogin = useForm({
  username: '',
  password: '',
});

const formOAuth = useForm({
  username: '',
  secretCode: props.secretCode || '',
  otp: '',
});

const step = ref(1);
const totalSteps = ref(2);

async function getOTP() {
  await formLogin.post(route('admin.onebss.login'), {
    preserveScroll: true,
    onSuccess: () => {
      if (props.secretCode) {
        formOAuth.username = formLogin.username;
        step.value++;
      }
    },
  });
}

async function login() {
  await formOAuth.post(route('admin.onebss.oauth'), {
    preserveScroll: true,
    onSuccess: () => {
      if (props.error) {

      } else {
        step.value--;
      }
    },
  });
}
</script>
