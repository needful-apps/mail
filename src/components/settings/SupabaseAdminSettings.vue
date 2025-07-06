<template>
  <div id="supabase-settings" class="section">
    <h2>Supabase</h2>

    <label for="supabase-url">Supabase URL</label>
    <input type="text" id="supabase-url" v-model="supabaseUrl">

    <label for="supabase-service-token">Supabase Service Token</label>
    <input type="text" id="supabase-service-token" v-model="supabaseServiceToken">

    <button @click="saveSettings">Save</button>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from '@nextcloud/axios';
import { generateUrl } from '@nextcloud/router';

export default {
  name: 'SupabaseAdminSettings',
  setup() {
    const supabaseUrl = ref('');
    const supabaseServiceToken = ref('');

    const fetchSettings = async () => {
      try {
        const response = await axios.get(generateUrl('/apps/mail/api/settings/supabase'));
        supabaseUrl.value = response.data.url;
        supabaseServiceToken.value = response.data.token;
      } catch (error) {
        console.error('Error fetching Supabase settings:', error);
      }
    };

    const saveSettings = async () => {
      try {
        await axios.post(generateUrl('/apps/mail/api/settings/supabase'), {
          url: supabaseUrl.value,
          token: supabaseServiceToken.value,
        });
        alert('Settings saved!');
      } catch (error) {
        console.error('Error saving Supabase settings:', error);
      }
    };

    onMounted(() => {
      fetchSettings();
    });

    return {
      supabaseUrl,
      supabaseServiceToken,
      saveSettings,
    };
  },
};
</script>
