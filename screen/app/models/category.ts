import { Competition } from './competition';
import { Team } from './team';

export class Category {
    id: number;
    name: string;
    competition: Competition;
    teams: Team[];

    constructor(id: number, name: string, competition: Competition, teams: Team[]) {
        this.id = id;
        this.name = name;
        this.competition = competition;
        this.teams = teams;
    }
}
