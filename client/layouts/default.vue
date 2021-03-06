<template>
  <v-app dark>
    <v-navigation-drawer
      v-model="drawer"
      fixed
      clipped
      app
    >
      <v-list>
        <v-list-item
          v-for="(item, i) in items"
          :key="i"
          :to="item.to"
          router
          exact
        >
          <v-list-item-action>
            <v-icon>{{ item.icon }}</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title v-text="item.title" />
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>
    <v-app-bar fixed clipped-left app>
      <v-app-bar-nav-icon @click.stop="drawer = !drawer" />
      <v-toolbar-title
        style="cursor:pointer"
        @click="$router.push('/')"
        v-text="title"
      />
      <v-spacer/>
      <div v-if="$auth.loggedIn">
        <v-btn
          icon
          to="/notifications"
        >
          <v-icon v-if="!unreadNotifications">
            mdi-bell
          </v-icon>
          <v-icon v-else color="red">
            mdi-bell-badge
          </v-icon>
        </v-btn>

        <v-btn
          class="mx-2"
          to="/projects/create"
        >
          新規作成
        </v-btn>
        <v-btn
          class="mx-2"
          @click="$auth.logout()"
        >
          ログアウト
        </v-btn>
      </div>
      <div v-else>
        <v-btn
          class="mx-2"
          to="/register"
        >
          新規登録
        </v-btn>
        <v-btn
          class="mx-2"
          to="/login"
        >
          ログイン
        </v-btn>
      </div>
      <v-btn
        v-if="$auth.loggedIn"
        icon
        @click.stop="rightDrawer = !rightDrawer"
      >
        <v-icon>mdi-menu</v-icon>
      </v-btn>
    </v-app-bar>
    <v-main>
      <v-container>
        <Nuxt />
      </v-container>
    </v-main>
    <v-navigation-drawer
      v-if="$auth.loggedIn"
      v-model="rightDrawer"
      fixed
      right
      temporary
    >
      <v-list>
        <v-list-item>
          <v-btn
            block
            :to="`/users/${$auth.user.id}`"
            :disabled="disabledButton"
          >
            プロフィール
          </v-btn>
        </v-list-item>
        <v-list-item>
          <v-btn
            block
            :to="`/users/${$auth.user.id}/following`"
          >
            フォロー中
          </v-btn>
        </v-list-item>
        <v-list-item>
          <v-btn
            block
            :to="`/users/${$auth.user.id}/followers`"
          >
            フォロワー
          </v-btn>
        </v-list-item>
        <v-divider/>
        <v-list-item>
          <v-btn
            block
            :disabled="disabledButton || ($auth.user.linked_with_twitter === 1)"
            :loading="linkWithTwitterLoading"
            @click="linkWithProvider('twitter')"
          >
            <v-icon class="mr-2" color="#1DA1F2">
              mdi-twitter
            </v-icon>
            <span>
              Twitterと連携
            </span>
          </v-btn>
        </v-list-item>
        <v-list-item>
          <v-btn
            block
            :disabled="disabledButton || ($auth.user.linked_with_google === 1)"
            :loading="linkWithGoogleLoading"
            @click="linkWithProvider('google')"
          >
            <v-icon class="mr-2" color="#4285F4">
              mdi-google
            </v-icon>
            <span>
              Googleと連携
            </span>
          </v-btn>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>
    <v-footer absolute app>
      <span>&copy; {{ new Date().getFullYear() }}</span>
    </v-footer>
  </v-app>
</template>

<script>
import { getAuth, GoogleAuthProvider, linkWithPopup, TwitterAuthProvider } from '@firebase/auth';

export default {
  name: 'DefaultLayout',
  data({ $auth, $axios }) {
    return {
      disabledButton: false,
      linkWithTwitterLoading: false,
      linkWithGoogleLoading: false,
      drawer: true,
      items: [
        {
          icon: 'mdi-account-group',
          title: 'ユーザー',
          to: '/users',
        },
        {
          icon: 'mdi-file-document',
          title: 'プロジェクト',
          to: '/projects',
        },
      ],
      right: true,
      rightDrawer: false,
      title: 'TEAMACTION',
      unreadNotifications: false,
    };
  },
  async mounted() {
    if (this.$auth.loggedIn) {
      const data = await this.$axios.$get(`api/data/unread-notifications/${this.$auth.user.id}`);
      this.unreadNotifications = data.notifications.length !== 0;

      window.Echo.channel('project-applied')
        .listen('ProjectApplied', (data) => {
          this.unreadNotifications = true;
        });
    }
  },
  methods: {
    async linkWithProvider($provider) {
      // 変数宣言
      let auth, provider;

      // ボタンの制限
      this.disabledButton = true;

      try {
        // 外部アカウントの連携
        auth = getAuth();
        switch ($provider) {
          case 'twitter':
            this.linkWithTwitterLoading = true;
            provider = new TwitterAuthProvider();
            break;
          case 'google':
            this.linkWithGoogleLoading = true;
            provider = new GoogleAuthProvider();
            break;
          default:
            throw new Error('エラー');
        }
        await linkWithPopup(auth.currentUser, provider);

        // ユーザー情報の更新
        await this.$axios.get(`/api/auth/${$provider}`);

        // 再読み込み
        location.reload();
      } catch (error) {
        this.linkWithTwitterLoading = false;
        this.linkWithGoogleLoading = false;
        this.disabledButton = false;
      }
    },
  },
}
</script>
