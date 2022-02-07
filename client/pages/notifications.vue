<template>
  <main>
    <v-card class="my-8 pa-8">
      <h1>お知らせ</h1>
      <v-list>
        <v-subheader class="black--text">
          <h2>未読のお知らせ</h2>
        </v-subheader>
        <v-list-item
          v-for="(unreadNotification, index) in unreadNotifications"
          :key="`unreadNotification${index}`"
        >
          <v-card class="my-4">
            <v-list>
              <v-list-item>
                <v-list-item-content>
                  <v-list-item-title>
                    プロジェクト{{ unreadNotification.project_id }}に参加リクエストがあります。
                  </v-list-item-title>
                </v-list-item-content>

                <v-list-item-icon v-if="unreadNotification.is_accepted === null">
                  <v-btn color="primary" @click="acceptRequest(unreadNotification)">
                    承認
                  </v-btn>
                </v-list-item-icon>

                <v-list-item-icon v-if="readNotification.is_accepted === null">
                  <v-btn color="red" @click="denyRequest(unreadNotification)">
                    拒否
                  </v-btn>
                </v-list-item-icon>
              </v-list-item>
            </v-list>
          </v-card>
        </v-list-item>
      </v-list>

      <v-list>
        <v-subheader class="black--text">
          <h2>既読のお知らせ</h2>
        </v-subheader>
        <v-list-item
          v-for="(readNotification, index) in readNotifications"
          :key="`readNotification${index}`"
        >
          <v-card class="my-4">
            <v-list>
              <v-list-item>
                <v-list-item-content>
                  <v-list-item-title>
                    プロジェクト{{ readNotification.project_id }}に参加リクエストがあります。
                  </v-list-item-title>
                </v-list-item-content>

                <v-list-item-icon v-if="readNotification.is_accepted === null">
                  <v-btn color="primary" @click="acceptRequest(readNotification)">
                    承認
                  </v-btn>
                </v-list-item-icon>

                <v-list-item-icon v-if="readNotification.is_accepted === null">
                  <v-btn color="red" @click="denyRequest(readNotification)">
                    拒否
                  </v-btn>
                </v-list-item-icon>
              </v-list-item>
            </v-list>
          </v-card>
        </v-list-item>
      </v-list>
    </v-card>
  </main>
</template>

<script>
export default {
  name: 'NotificationsPage',
  async asyncData({ $auth, $axios }) {
    const data = await $axios.$get(`/api/data/notifications/${$auth.user.id}`);

    return {
      readNotifications: data.readNotifications,
      unreadNotifications: data.unreadNotifications,
    }
  },
  methods: {
    acceptRequest($notification) {
      $notification.is_accepted = 1;
      this.$axios.$patch(`/api/accept/request/${$notification.user_id}/${$notification.project_id}`);
    },
    denyRequest($notification) {
      $notification.is_accepted = 0;
      this.$axios.$patch(`/api/deny/request/${$notification.user_id}/${$notification.project_id}`);
    },
  },
}
</script>
