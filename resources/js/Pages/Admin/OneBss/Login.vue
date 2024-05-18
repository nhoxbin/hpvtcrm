<template>
  <Head title="Khách hàng"/>

  <AuthenticatedLayout>
    <template #header>
      Đăng nhập OneBss
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
          <InputError class="mt-2" :message="formOAuth.errors.secretCode" />
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

    <div class="p-4 bg-white rounded-lg shadow-xs mt-5">
      <div class="overflow-hidden mb-8 w-full rounded-lg border shadow-xs">
        <div class="overflow-x-auto w-full">
          <table class="w-full whitespace-no-wrap">
            <thead>
            <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase bg-gray-50 border-b">
              <th class="px-4 py-3">Username</th>
              <th class="px-4 py-3">Phiên làm việc</th>
              <th class="px-4 py-3">Access Token</th>
              <th class="px-4 py-3">Thao tác</th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y">
              <tr v-for="account in accounts.data" :key="account.id" class="text-gray-700">
                <td class="px-4 py-3 text-sm">
                  {{ account.username }}
                </td>
                <td class="px-4 py-3 text-sm">
                  {{ account.expires_at }}
                </td>
                <td class="px-4 py-3 text-sm">
                  <button type="button" v-show="account.access_token.length" @click="copyURL(account.access_token)" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">Copy</button>
                </td>
                <td class="px-4 py-3 text-sm">
                  <div class="inline-flex rounded-md shadow-sm" role="group">
                    <button @click="del(account.id)" type="button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-e-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white">
                      <svg class="h-3 w-3 text-red-500"  width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">  <path stroke="none" d="M0 0h24v24H0z"/>  <line x1="4" y1="7" x2="20" y2="7" />  <line x1="10" y1="11" x2="10" y2="17" />  <line x1="14" y1="11" x2="14" y2="17" />  <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />  <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="!accounts.total">
                <td class="px-4 py-3 text-sm text-center" colspan="4">Không có dữ liệu</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase bg-gray-50 border-t sm:grid-cols-9">
          <pagination :links="accounts.links" />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/Admin/AuthenticatedLayout.vue';
import Pagination from '@/Components/Admin/Pagination.vue';
import 'element-plus/es/components/message/style/css';
import 'element-plus/es/components/message-box/style/css';
// import { ElMessage, ElMessageBox } from 'element-plus';
// import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButton from '@/Components/Admin/PrimaryButton.vue';
import { router, useForm } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
// import { Link } from '@inertiajs/vue3';

import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref } from 'vue';

const props = defineProps({
  accounts: Object,
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
  secretCode: '',
  otp: '',
});

const step = ref(1);
const totalSteps = ref(2);

async function getOTP() {
  await formLogin.post(route('admin.onebss.accounts.login'), {
    preserveScroll: true,
    onSuccess: () => {
      if (props.secretCode) {
        formOAuth.username = formLogin.username;
        formOAuth.secretCode = props.secretCode;
        step.value++;
      }
    },
  });
}

async function login() {
  await formOAuth.post(route('admin.onebss.accounts.oauth'), {
    preserveScroll: true,
    onSuccess: () => {
      if (props.error) {

      } else {
        step.value--;
        router.reload({only: ['accounts']});
      }
    },
  });
}

const del = (account) => {
  ElMessageBox.confirm(
    'Bạn có chắc muốn xóa tài khoản này chứ?',
    'Warning',
    {
      confirmButtonText: 'OK',
      cancelButtonText: 'Cancel',
      type: 'warning',
    }
  ).then(() => {
    axios.delete(route('admin.onebss.accounts.destroy', account)).then(({data}) => {
      ElMessage({
        type: 'success',
        message: data.msg,
      });
      router.reload({only: ['accounts']});
    }).catch(function(err) {
      console.log(err.responseText);
    });
  }).catch(() => {
    /* ElMessage({
      type: 'info',
      message: 'Delete cancelled',
    }) */
  })
}

async function copyURL(mytext) {
  var input = document.createElement('input');
  input.setAttribute('value', mytext);
  input.value = mytext;
  document.body.appendChild(input);
  try {
    await input.focus();
    await input.select();
    var successful = document.execCommand('copy');
    var msg = successful ? 'successful' : 'unsuccessful';
    ElMessage({
      type: 'success',
      message: 'Copied ' + msg,
    });
  } catch (err) {
    console.log('Oops, unable to copy');
  }
  document.body.removeChild(input);
}
</script>
