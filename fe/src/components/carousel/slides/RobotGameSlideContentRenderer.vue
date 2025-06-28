<script setup>
import {RobotGameSlideContent} from '../../../model/robotGameSlideContent.ts';

const props = defineProps({
  content: RobotGameSlideContent
});

// testdata
const teams = [{
  name: 'Here We GO',
  rank: 1,
  scores: [{points: 495, highlight: false}, {points: 530, highlight: true}, {points: 470, highlight: false}]
}, {
  name: 'GO Robot',
  rank: 2,
  scores: [{points: 250, highlight: false}, {points: 280, highlight: false}, {points: 320, highlight: true}]
}];
</script>

<template>
  <div class="slide-container">
    <h1 class="slide-title">Robot Game Scores</h1>
    <div>
      <table class="scores">
        <thead>
        <tr>
          <th class="teamName">Team</th>
          <th class="cell">R I</th>
          <th class="cell">R II</th>
          <th class="cell">R III</th>
          <th class="cell">Rank</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="team in teams" :key="team.id">
          <td class="teamName">{{ team.name }}</td>
          <td class="cell">{{ team.scores[0].points }}</td>
          <td class="cell">{{ team.scores[1].points}}</td>
          <td class="cell">{{ team.scores[2].points }}</td>
          <td class="cell">{{ team.rank }}</td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<style scoped>
.slide-container {
  width: 100%;
  height: 100%;
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: start;
  background-size: cover;
  background-position: center;
  /* TODO: Load background image from server and use url defined by slide content */
  /*background-image: url('~@/assets/Background.png');*/
  color: black; /* Use value from settings */
}

.slide-title {
  font-size: 3rem;
  font-weight: bold;
  padding: 3rem 1rem 3rem 1rem;
}

.scores {
  width: 100%;
  border-collapse: collapse;
  table-layout: fixed;
  font-size: 2rem;
  line-height: 1;
}

th, td {
  padding: 0.5rem 1rem 0.5rem 1rem;
}

.teamName {
  border-right: 1px solid white;
  border-bottom: 1px solid white;
  width: auto;
}
.cell {
  width: 10rem;
}
</style>
