<template>
  <main>
    <v-list>
      <h1>フォロワー</h1>
      <v-row>
        <v-col
          v-for="(followerUser, index) in followerUsers"
          :key="`followingUser${index}`"
          cols="3"
          class="my-4"
        >
          <v-card>
            <v-list>
              <v-list-item>
                <v-list-item-avatar size="50">
                  <v-img
                    :src="followerUser.usericon_image_uri"
                  />
                </v-list-item-avatar>

                <v-list-item-content>
                  <a :href="`/users/${followerUser.id}`" class="black--text">
                    {{ followerUser.username }}
                  </a>
                </v-list-item-content>
              </v-list-item>
            </v-list>
          </v-card>
        </v-col>
      </v-row>
    </v-list>

    <div class="text-center">
      <v-pagination
        v-model="page"
        class="pt-4"
        circle
        color="light-green accent-4"
        :length="pageLength"
        :total-visible="7"
        @input="changePage"
      ></v-pagination>
    </div>
  </main>
</template>

<script>
export default {
  name: 'UserFollowingPage',
  async asyncData({ params, route, $axios }) {
    const id = params.id;
    const group = route.query.group ?? 1;
    const data = await $axios.$get(`api/data/users/followers/${id}/${group}`);
    const pageLength = Math.ceil(data.count / 24);

    return {
      followerUsers: data.followerUsers,
      page: Number(group),
      pageLength,
    }
  },
  watch: {
    $route() {
      location.reload();
    },
  },
  methods: {
    changePage(index) {
      this.$router.push(`/users/${this.$auth.user.id}/followers?group=${index}`);
    },
  },
}
</script>
