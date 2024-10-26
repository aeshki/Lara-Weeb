<script setup>
import { reactive, ref, watch } from 'vue';
import { useAuthStore } from '@/stores/auth';
import useAxios from '@/utils/useAxios';
import { useRouter } from 'vue-router';

import HeaderNav from '@/components/layout/HeaderNavbar.vue';

import {
  AreaInput,
  RoundedButton,
  DefaultButton,
  TextInput,
  FileInput,
  SwitchInput,
  ColorInput
} from '@/components/common';

import {
  UserAvatar,
  UserBanner
} from '@/components/User';

const router = useRouter();
const authStore = useAuthStore();

const form = reactive({
  avatar: authStore.user.avatar,
  banner: authStore.user.banner,
  banner_color: authStore.user.banner_color,
  pseudo: authStore.user.pseudo,
  username: authStore.user.username,
  bio: authStore.user.bio,
  email: authStore.user.email,
  favorite_anime: authStore.user.favorite_anime,
  favorite_manga: authStore.user.favorite_manga,
  favorite_webtoon: authStore.user.favorite_webtoon,
  is_private: authStore.user.is_private,
});

const avatarPreview = ref(null);
const bannerPreview = ref(null);

watch(() => form.avatar, (avatar) => {
  if (avatar instanceof File) {
    previewImage(avatar, avatarPreview);
  }
});

watch(() => form.banner, (banner) => {
  if (banner instanceof File) {
    previewImage(banner, bannerPreview);
  }
});

const previewImage = (file, ref) => {
  const reader = new FileReader();

  reader.onload = (e) => {
    ref.value = e.target.result;
  };

  reader.readAsDataURL(file);
};

const handleRemoveAvatar = () => {
  if (avatarPreview.value) {
    avatarPreview.value = null;
    form.avatar = authStore.user.avatar;
  } else {
    form.avatar = form.avatar ? null : authStore.user.avatar;
  }
};

const handleRemoveBanner = () => {
  if (bannerPreview.value) {
    bannerPreview.value = null;
    form.banner = authStore.user.banner;
  } else {
    form.banner = form.banner ? null : authStore.user.banner;
  }
};

const { loading, errors, fetchRequest, onFulfilled } = useAxios('/api/users/@me', {
        method: 'POST',
        deferred: true
    });

const handleSubmit = () => {
  let formData = new FormData();

  formData.set('_method', 'PATCH');

  Object.entries(form).forEach(data => {
      data[1] ??= '';

      if ((data[0] === 'avatar' || data[0] === 'banner') && !(data[1] instanceof File) && data[1]) {
          return
      } else if (typeof data[1] === 'boolean') {
          data[1] = +data[1]
      }

      formData.append(data[0], data[1]);
  });

  fetchRequest(formData);
}

const handleLogout = () => {
  const { onFulfilled } = useAxios('/auth/logout');

  onFulfilled(() => {
    router.push('/auth/login');
  });
};

const handleDeleteAccount = () => {
  if (confirm('Voulez-vous vraiment supprimer votre compte ?')) {
    const { onFulfilled } = useAxios('/api/users/@me', { method: 'DELETE' });
  
    onFulfilled(() => {
      router.push('/auth/login');
    });
  }
};

onFulfilled((data) => {
  router.push(`/@${data.value.user.username}`).then(() => {
    location.reload();
  });
});
</script>

<template>
  <div>
    <HeaderNav title='Éditer le profil' class='sticky top-0 backdrop-blur-lg bg-zinc-900/80' />  
    <div class='flex flex-col gap-4 p-4'>
      <form @submit.prevent='handleSubmit' class='flex flex-col gap-4'>
        <div class='flex flex-col gap-3'>
          <label class='font-semibold uppercase text-sm'>Bannière</label>

          <UserBanner
            :path='form.banner === authStore.user.banner ? form.banner : null'
            :absolutePath='bannerPreview'
            :color='form.banner_color'
          />

          <div class='flex flex-col mobileLarge:flex-row mobileLarge:items-center gap-2'>
            <FileInput
              id='banner'
              :label='form.banner ? "Changer de bannière" : "Ajouter une bannière"'
              :error='errors?.banner?.[0]'
              v-model='form.banner'
            />

            <RoundedButton
              v-if='bannerPreview || authStore.user.banner !== null'
              :text="bannerPreview ? `Revenir à la bannière de base` : form.banner === authStore.user.banner ? `Supprimer la bannière` : 'Remettre la bannière'"
              @click.prevent='handleRemoveBanner'
            />
          </div>

          <ColorInput
              id='banner'
              label='Couleur de la bannière'
              v-model='form.banner_color'
            />
        </div>

        <div class='flex flex-col gap-3'>
          <label class='font-semibold uppercase text-sm'>Avatar</label>

          <div class='flex gap-3'>
            <UserAvatar
              :size='64'
              :path='form.avatar === authStore.user.avatar ? form.avatar : null'
              :absolutePath='avatarPreview'
              :username='form.username'
              :gif-static='false'
            />

            <div class='flex flex-col mobileLarge:flex-row mobileLarge:items-center gap-2'>
              <FileInput
                id='avatar'
                :label="form.avatar ? `Changer d'avatar` : 'Ajouter un avatar'"
                :error='errors?.avatar?.[0]'
                v-model='form.avatar'
              />

              <RoundedButton
                v-if='avatarPreview || authStore.user.avatar !== null'
                :text="avatarPreview ? `Revenir à l'avatar de base` : form.avatar === authStore.user.avatar ? `Supprimer l'avatar` : `Remettre l'avatar`"
                @click.prevent='handleRemoveAvatar'
              />
            </div>
          </div>
        </div>

        <TextInput
          id='pseudo'
          label="Nom d'affichage"
          :disabled='loading'
          :error='errors?.pseudo?.[0]'
          v-model='form.pseudo'
        />
        
        <TextInput
          id='username'
          label="Nom d'utilisateur"
          :disabled='loading'
          required
          :error='errors?.username?.[0]'
          v-model='form.username'
        />
      
        <AreaInput
          id='Bio'
          label='Bio'
          :disabled='loading'
          :error='errors?.bio?.[0]'
          v-model='form.bio'
        />
      
        <TextInput
          id='email'
          type='email'
          label='E-Mail'
          :disabled='loading'
          :error='errors?.email?.[0]'
          required
          v-model='form.email'
        />
        
        <TextInput
          id='favorite_anime'
          label='Anime favoris'
          :disabled='loading'
          v-model='form.favorite_anime'
        />
      
        <TextInput
          id='favorite_manga'
          label='Manga favoris'
          :disabled='loading'
          v-model='form.favorite_manga'
        />
        
        <TextInput
          id='favorite_webtoon'
          label='Webtoon favoris'
          :disabled='loading'
          v-model='form.favorite_webtoon'
        />

        <SwitchInput
          id='is_private'
          label='Profil privé'
          :disabled='loading'
          v-model='form.is_private'
        />
        
        <DefaultButton text='Sauvegarder' :loading='loading' />
      </form>

      <div class='flex gap-2'>
        <RoundedButton text='Déconnexion' @click='handleLogout' />
        <RoundedButton text='Suppression de compte' @click='handleDeleteAccount' />
      </div>
    </div>
  </div>
</template>
