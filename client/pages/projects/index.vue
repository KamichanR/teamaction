<template>
  <main>
    <div class="d-flex flex-row my-4">
      <v-card class="mr-4">
        <v-select
          v-model="selected.projectCategory"
          :items="projectCategories"
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
        @click="searchProjects"
      >
        検索
      </v-btn>
    </div>

    <v-row>
      <v-col
        v-for="(project, projectIndex) in projects"
        :key="`project${projectIndex}`"
        cols="4"
      >
        <v-card height="400">
          <v-img
            :aspect-ratio="16/9"
            :src="project.project_image_uri"
          />
          <v-list>
            <v-list-item>
              <v-list-item-content>
                <v-list-item-title>
                  <a :href="`/projects/${project.id}`" class="black--text text-h6">
                    {{ project.project_title }}
                  </a>
                </v-list-item-title>
              </v-list-item-content>
            </v-list-item>

            <v-list-item>
              <v-list-item-avatar>
                <v-img
                  :src="project.user.usericon_image_uri"
                />
              </v-list-item-avatar>

              <v-list-item-content>
                <v-list-item-title>
                  {{ project.user.username }}
                </v-list-item-title>
              </v-list-item-content>
            </v-list-item>

            <v-list-item>
              <v-list-item-subtitle v-text="project.description" />
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
  name: 'ProjectsPage',
  async asyncData({ route, $axios }) {
    const group = route.query.group ?? 1;
    const data = await $axios.$get(`api/data/projects/${group}`, {
      params: {
        projectCategory: route.query.projectCategory,
        area: route.query.area,
        skill: route.query.skill,
      }
    });
    const projects = data.projects;
    const pageLength = Math.ceil(data.count / 12);

    return {
      projects,
      page: Number(group),
      pageLength,
      areas: data.areas,
      skills: data.skills,
      projectCategories: data.projectCategories,
      selected: {
        projectCategory: '',
        area: '',
        skill: '',
      },
    };
  },
  methods: {
    changePage(page) {
      location.href = `/projects?group=${page}`;
    },
    searchProjects() {
      const uri = new URL('http://localhost:3000/projects');
      if (this.selected.projectCategory) uri.searchParams.set('projectCategory', this.selected.projectCategory.id);
      if (this.selected.area) uri.searchParams.set('area', this.selected.area.id);
      if (this.selected.skill) uri.searchParams.set('skill', this.selected.skill.id);
      location.href= uri.toString();
    }
  },
}
</script>
