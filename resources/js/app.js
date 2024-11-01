import { createApp } from 'vue';
import RingtoneForm from './components/RingtoneForm.vue';

const app = createApp({});
app.component('ringtone-form', RingtoneForm);
app.mount('#app');

