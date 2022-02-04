<template>
  <main>
    <div class="d-flex flex-row my-4">
      <v-card class="mr-4">
        <v-select
          v-model="selected.workstyle"
          :items="workstyles"
          clearable
          dense
          hide-details
          item-text="name"
          label="カテゴリ"
          outlined
          required
          return-object
        />
      </v-card>
      <v-card class="mr-4">
        <v-select
          v-model="selected.area"
          :items="areas"
          clearable
          dense
          hide-details
          item-text="name"
          label="活動場所"
          outlined
          required
          return-object
        />
      </v-card>
      <v-card class="mr-4">
        <v-select
          v-model="selected.skill"
          :items="skills"
          clearable
          dense
          hide-details
          item-text="name"
          label="スキル"
          outlined
          required
          return-object
        />
      </v-card>

      <v-btn
        class="mr-4"
        @click="searchUsers"
      >
        検索
      </v-btn>
    </div>

    <v-row>
      <v-col
        v-for="(user, userIndex) in users"
        :key="`user${userIndex}`"
        cols="3"
      >
        <v-card height="400">
          <v-list>
            <v-list-item>
              <v-list-item-avatar size="100%">
                <v-img
                  :aspect-ratio="1/1"
                  :src="user.usericon_image_uri"
                />
              </v-list-item-avatar>
            </v-list-item>

            <v-list-item>
              <v-list-item-content>
                <v-list-item-title class="font-weight-medium text-center text-h5">
                  <a :href="`/users/${user.id}`" class="black--text">
                    {{ user.username }}
                  </a>
                </v-list-item-title>

                <v-list-item-subtitle
                  v-for="(skill, skillIndex) in user.skills"
                  :key="`skill${userIndex}-${skillIndex}`"
                  class="text-center"
                >
                  {{ skill.name }}
                </v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
          </v-list>
        </v-card>
      </v-col>
    </v-row>

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
  name: 'UsersPage',
  async asyncData({ route, $axios }) {
    const group = route.query.group ?? 1;
    const data = await $axios.$get(`api/data/users/${group}`, {
      params: {
        workstyle: route.query.workstyle,
        area: route.query.area,
        skill: route.query.skill,
      }
    });
    const users = data.users;
    const pageLength = Math.ceil(data.count / 16);

    return {
      users,
      page: Number(group),
      pageLength,
      workstyles: data.workstyles,
      skills: data.skills,
      areas: data.areas,
      selected: {
        workstyle: '',
        area: '',
        skill: '',
      }
    };
  },
  methods: {
    changePage(page) {
      location.href = `/users?group=${page}`;
    },
    searchUsers() {
      const uri = new URL('http://localhost:3000/users');
      if (this.selected.workstyle) uri.searchParams.set('workstyle', this.selected.workstyle.id);
      if (this.selected.area) uri.searchParams.set('area', this.selected.area.id);
      if (this.selected.skill) uri.searchParams.set('skill', this.selected.skill.id);
      location.href= uri.toString();
    },
  },
}
</script>
