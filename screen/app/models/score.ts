import { Team } from './team';

export class Score {
    points: number;
    time: number;
    team: Team;
    highlight: boolean;

    constructor(points: number, time: number, team: Team, highlight: boolean) {
        this.points = points;
        this.time = time;
        this.team = team;
        this.highlight = highlight;
    }
}
